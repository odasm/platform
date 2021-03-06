<?php
require_once ROOT . DS . 'vendors' . DS . 'GeoIP2-php' . DS . 'vendor' . DS . 'autoload.php';
    class DetectIp {
        public function publicClientIp()
        {
            $ip = Router::getRequest()->clientIp();
            if ($this->isPrivateIp($ip)) {
                $ip = Router::getRequest()->clientIp(false);
            }

            if( Router::getRequest()->data('ip') ){
                $ip = Router::getRequest()->data('ip');
            }

            $ip = explode(',', $ip);
            if( !empty($ip[0]) ) $ip = $ip[0];

            CakeLog::info('detect ip:' . print_r($ip,true), 'user');

            return $ip;
        }

        public function isPrivateIp($ip)
        {
            $pri_addrs = array(
                '192.168.0.0|192.168.255.255',
                '127.0.0.0|127.255.255.255'
            );

            $long_ip = ip2long($ip);
            if($long_ip != -1) {
                foreach($pri_addrs AS $pri_addr)
                {
                    list($start, $end) = explode('|', $pri_addr);

                    // IF IS PRIVATE
                    if($long_ip >= ip2long($start) && $long_ip <= ip2long($end))
                        return true;
                }
            }

            return false;
        }
    }