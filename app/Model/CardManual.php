<?php

App::uses('AppModel', 'Model');

class CardManual extends AppModel {

	public $useTable = 'card_manuals';

    public $actsAs = array(
        'Search.Searchable'
    );

    public $validate = array(
        'card_serial' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter a card seria'
            )
        ),
		'card_code' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter a card code'
            )
        ),
        'card_price' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Please chose a price'
            )
        ),
        'price' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Please chose a price'
            )
        ),
        'type' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Please chose a type'
            )
        ),
    );

    public $filterArgs = array(
        'order_id'  => array('type' => 'value'),
        'game_id'   => array('type' => 'value'),
        'status'    => array('type' => 'value'),
        'chanel'    => array('type' => 'value'),
        'type'      => array('type' => 'value'),
        'username'  => array('type' => 'like', 'field' => array('User.id', 'User.username', 'User.email')),
        'cardnumber'=> array('type' => 'value', 'field' => 'card_serial'),
        'cardcode'  => array('type' => 'value', 'field' => 'card_code'),
        'from_time' => array('type' => 'expression', 'method' => 'fromTimeCond', 'field' => 'CardManual.time >= '),
        'to_time'   => array('type' => 'expression', 'method' => 'toTimeCond', 'field' => 'CardManual.time <= '),

    );

    public function fromTimeCond($data = array())
    {
        return date('U', strtotime(date('d-m-Y 0:0:0', $data['from_time'])));
    }

    public function toTimeCond($data = array())
    {
        return date('U', strtotime(date('d-m-Y 23:59:59', $data['to_time'])));
    }

    function paginateCount($conditions = array(), $recursive = 0, $extra = array())
    {
        $parameters = compact('conditions');
        if ($recursive != $this->recursive) {
            $parameters['recursive'] = $recursive;
        }

        $extra['recursive'] = -1;
        $extra['contain'] = array();
        if(isset($conditions['OR']['User.username LIKE'])    ||
            isset($conditions['OR']['User.email LIKE'])
        ){
            $this->bindModel(array(
                'belongsTo' => array('User')
            ));
            $extra['contain'] = array_merge($extra['contain'], array('User'));
        }

        return $this->find('count', array_merge($parameters, $extra));
    }
}
