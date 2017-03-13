<?php
if (version_compare(Phpfox::getVersion(), '4.5.0', '<') && $this->_sModule == 'elmoney') {
    \Core\Route\Controller::$name = false;
}