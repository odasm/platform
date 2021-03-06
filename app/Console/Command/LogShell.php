<?php

App::uses('ClassRegistry', 'Utility');
App::uses('AppShell', 'Console/Command');
App::uses('ConnectionManager', 'Model');
App::import('Lib', 'RedisQueue');

/**
 * Save data (not just logs) in mysql by use redis queue
 **/
class LogShell extends AppShell {

	public $uses = array('User');
	/** 
	 * A queue job that save logs
	 **/
	public function save()
	{
		$this->out('Starting..');
		try {

			$Redis = new RedisQueue();

			$starttime = time();

			while (true) {

				$log = $Redis->lPop();

				if ($log == false && !empty($_SERVER['testing'])) {
					break;
				}
				$this->out(date('Y-m-d H:i:s') . " - Redis pop");
				# run this command 60s only
				if ((time() - $starttime) > 60) {
					break;
				}

				if (!$log) {
					$this->out('Not Found..');
					sleep(1);
				} else {
					$start = microtime(true);
					$this->out($Redis->lSize() . ' is remaining..');

					# save use specific method or only basic save method
					if(!empty($log['model'])){
						$modelObject = ClassRegistry::init($log['model']);
						if (empty($log['data']['id']) && empty($log['data'][$log['model']]['id']))
							$modelObject->create();

						try {
							if (method_exists($this, '__' . $log['model'])) {
								call_user_func(array($this, '__' . $log['model']), $modelObject, $log['data']);
							} else {
								$modelObject->save($log['data'], array('callbacks' => false, 'validate' => false));
							}
							$this->out('	Saved: ' . $log['model'] . ' in:' . ((microtime(true) - $start) * 1000) . 'ms');
						} catch (Exception $e) {
							CakeLog::info('Can not save log' . $e->getMessage());
						}
					}

					if(!empty($log['type'])){
						try {
							if (method_exists($this, '__type' . $log['type'])) {
								call_user_func(array($this, '__type' . $log['type']), $log);
							}else{
								CakeLog::info('Can not save log');
							}

							$this->out('	Saved: ' . $log['type'] . ' in:' . ((microtime(true) - $start) * 1000) . 'ms');
						}catch (Exception $e){
							CakeLog::info('Can not save log' . $e->getMessage());
						}
					}
				}
			}
		} catch (Exception $e) {
			CakeLog::info('Can not save log' . $e->getMessage());
		}
		$this->out('End..');
	}

	private function __LogLogin($modelObject, $data)
	{
		if (!$modelObject->save(array(
			'LogLogin' => $data
		), true, array(
			'os', 'resolution','sdk_ver',
			'g_ver', 'device', 'user_id', 'network', 'game_id', 'ip', 'created'
		))) {
			throw new Exception(print_r($modelObject->validationErrors, true));
		}
	}

	private function __LogEntergame($modelObject, $data)
	{	
		if (!$modelObject->save(array(
			'LogEntergame' => $data 
		), true, array(
			'os', 'resolution','sdk_ver', 'g_ver', 'device',
			'user_id','network', 'game_id', 'ip', 'area_id',
			'role_id', 'distributor', 'created'
		))) {
			throw new Exception('Khong the save Info'
				. print_r($modelObject->validationErrors, true)
				. print_r($data, true)
			);
		}
	}

	public function test(){
        CakeResque::enqueue(
            'default',
            'Log',
            array('test2')
        );
    }

    public function test2(){
        CakeLog::info('test cakeresque');

        $text = 'Vm1JbzB6RDFsUHhQT0NGdk4vaFFnajg3MXVkVWR6MHJBek1BMjNVaGo5T2RzMTVjTjFXVTJOdDZYVFByUXMwNnVwdXhrOWdSU0pIcXk0dUsvSWlJdndMMG5oMkMySSt2Wjd1UVYvMlVlTVJWUHhrUXM4V2NQZmJPYU5ObkhPaVh4bm1ROTkrUG4vQldVeUtxditHVmNLNFQvYkg2cFRhbTJwQ3IvNkYzU2FBPQ==';

        $mc_token = 'tqtLWMqnKkqi3NRP32amXwSxJuFOCL';
        $mc_checksum = 'Sj21QrpiNpI6DrFutfRWUetCwCK4CU';
        $mc_encrypt = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCIZlME8jWIGDQRmLQxmw/8Gd8vgcoHLPNoaAnmq8WKvQb2Tk6uI0wyOqOI2IHNZm/k5Wz6NQvsiFgLWTXhtpyvaMfAFLQzc9cYWy6yBd+56QGYiYIMJdsR1wIkBZLQ5UPQleVXrnyhs1NPnZVJU0BsRurmQiHFSi1mHqtiZUQ1RQIDAQAB';
        App::uses('AlePay', 'Payment');
        $aleObj = new AlePay($mc_token, $mc_checksum, $mc_encrypt, 'a', 'b');

        $tranNL = $aleObj->decrypt(base64_decode($text));
//        $tran_id = 'ALE0058QY';
//        $tranNL = $aleObj->getTransactionDetail($tran_id);

        debug($tranNL);
    }
}