<?php
class MY_Controller extends MX_Controller
{

	public $ctl;
	public $method;
	public $data = array();

	function __construct()
	{
		parent::__construct();
		$this->load->module('template');

		$this->ctl = get_class($this);
		$this->method = $this->uri->segment(2);

		$this->data['ctl'] = $this->ctl;

		$this->data['page'] = $this->ctl;

		//load model
		$model = $this->ctl.'/M_'.$this->ctl;
		$this->load->model($model);
	}
}