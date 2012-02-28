<?php 
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE {$installer->getTable('Orderexport/orderexport')}(
`orderexport_id` int(11) NOT NULL auto_increment,
`file` text NOT NULL,
`count` int(11) NOT NULL default '0',
`created` datetime default NULL,
PRIMARY KEY (`orderexport_id`))
 ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
?>