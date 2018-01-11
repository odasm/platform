<?php
App::uses('AppController', 'Controller');

/**
 * Bonus Controller
 *
 * @property Bonus              $Bonus
 * @property PaginatorComponent $Paginator
 */
class BonusesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = ['Paginator', 'Search.Prg'];

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->layout = 'default_bootstrap';

		if (!empty($this->request->params['prefix']) && $this->request->params['prefix'] == 'admin') {
			$this->Bonus->enablePublishable('find', false);
		}
	}

	public function admin_index()
	{
		$this->Prg->commonProcess();

		$this->request->data['Bonus'] = $this->passedArgs;

		if ($this->Bonus->Behaviors->loaded('Searchable')) {
			$parsedConditions = $this->Bonus->parseCriteria($this->passedArgs);
		} else {
			$parsedConditions = array();
		}
		if (	!empty($this->passedArgs)
			&&	empty($parsedConditions)
		) {
			if (	(count($this->passedArgs) == 1 && empty($this->passedArgs['page']))
			) {
				$this->Session->setFlash("Can not find anyone match this conditions", "error");
			}
		}

		$games = $this->Bonus->Game->find('list', array(
			'fields' => array('id', 'title_os'),
			'conditions' => array(
				'Game.id' => $this->Session->read('Auth.User.permission_game_default'),
			)
		));

		$this->paginate = array(
			'Bonus' => array(
				'fields' => array('Bonus.*', 'User.username', 'User.id', 'Game.title', 'Game.os'),
				'conditions'    => $parsedConditions,
				'order'         => array('Bonus.id' => 'DESC'),
				'contain'       => array('Game', 'User'),
				'recursive'     => -1
			)
		);

		$compense = $this->paginate();

		$this->set(compact('games', 'compense'));

		# set contain in view
		$this->loadModel('Payment');
	}

	public function admin_add()
	{
		$this->loadModel('Game');
		$this->loadModel('Payment');

		$games = $this->Bonus->Game->find('list', array(
			'fields' => array('id', 'title_os'),
			'conditions' => array(
				'Game.id' => $this->Session->read('Auth.User.permission_game_default'),
			)
		));

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = [
				'order_id' => microtime(true) * 10000,
				'user_id' => $this->request->data['Bonus']['user_id'],
				'game_id' => $this->request->data['Bonus']['game_id'],
				'price' => 0,
				'bonus' => $this->request->data['Bonus']['bonus'],
				'status' => 0,
				'chanel' => Payment::CHANEL_BONUS,
			];

			try {
				if ($this->Bonus->save($data)) {
					$this->Session->setFlash('Bonus Payment has been saved');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('Bonus Payment could not be saved. Please, try again.');
				}
			}catch (Exception $e){
				CakeLog::error('save Bonus Payment add error: '.$e->getMessage());
				$this->Session->setFlash('Bonus Payment could not be saved. Please, try again.');
			}
		}

		$this->set(compact('games'));
	}
}
