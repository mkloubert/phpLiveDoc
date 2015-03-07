<?php


/**
 * My fantastic own interface.
 * 
 * @author Marcel Kloubert
 *
 */
interface IMyInterface extends \Iterator {
	/**
	 * The method wirh the name B.
	 */
	function methodB();
	
	/**
	 * A method that is called 'A'
	 * 
	 * @param array $a1 First argument 
	 * @param callable $a3 3rd one
	 */
	function methodA($a3, array $a1 = array(), $a2 = null, $a4 = 'TM');
}

/**
 * My nice test class.
 * 
 * @author Marcel Kloubert
 *
 */
class TestClass1 {
	const MK_CONSTANT = 23979;
}

/**
 * My second class for testing.
 * 
 * @author Marcel Kloubert
 *
 */
abstract class TestClass2 extends TestClass1 implements IMyInterface {
	const TM_CONSTANT = 'TM';
	
	/**
	 * My 2nd test prop
	 * 
	 * @var integer
	 */
	public $TestProp2 = 23979;
	/**
	 * My test prop
	 *
	 * @var string
	 */
	public $TestProp1 = 'YS';
}

final class TestClass3 extends TestClass1 {
}

