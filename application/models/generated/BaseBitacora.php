<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Bitacora', 'doctrine');

/**
 * BaseBitacora
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $tabla_id
 * @property integer $usuario_id
 * @property string $modelo
 * @property string $accion
 * @property string $referencia
 * @property Usuario $Usuario
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBitacora extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('bitacora');
        $this->hasColumn('tabla_id', 'integer', 8, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '8',
             ));
        $this->hasColumn('usuario_id', 'integer', 8, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 8,
             ));
        $this->hasColumn('modelo', 'string', 100, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '100',
             ));
        $this->hasColumn('accion', 'string', 100, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '100',
             ));
        $this->hasColumn('referencia', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Usuario', array(
             'local' => 'usuario_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}