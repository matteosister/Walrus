<?php
/**
 * User: matteo
 * Date: 25/09/12
 * Time: 15.15
 *
 * Just for fun...
 */

namespace Walrus\Utilities;

use Walrus\Utilities\Utilities,
    Walrus\WalrusTestCase;

class UtilitiesTest extends WalrusTestCase
{
    public function testSlugify()
    {
        $u = new Utilities();
        $this->assertEquals('test', $u->slugify('test'));
        $this->assertEquals('test', $u->slugify('test?'));
        $this->assertEquals('test', $u->slugify('?test'));
        $this->assertEquals('test', $u->slugify('?test?'));
        $this->assertEquals('test-test', $u->slugify('test?test'));
        $this->assertEquals('test-test', $u->slugify('test????test'));
        $this->assertEquals('test-test', $u->slugify('test????tèst'));
        $this->assertEquals('test', $u->slugify('test^'));
        $this->assertEquals('test2', $u->slugify('test2'));
        $this->assertEquals('test', $u->slugify('Test'));
        $this->assertEquals('test', $u->slugify('TesT'));
        $this->assertEquals('test', $u->slugify('TEST'));
        $this->assertEquals('test', $u->slugify('TEST '));
        $this->assertEquals('n-a', $u->slugify(''));
    }

    public function testGetUniqueSlug()
    {
        $u = new Utilities();
        $this->assertEquals('test', $u->getUniqueSlug(array(), 'test'));
        $this->assertEquals('test-1', $u->getUniqueSlug(array('test'), 'test'));
        $this->assertEquals('test-2', $u->getUniqueSlug(array('test', 'test-1'), 'test'));
        $this->assertEquals('test-2', $u->getUniqueSlug(array('test', 'test-1'), 'test?'));
        $this->assertEquals('test-2', $u->getUniqueSlug(array('test', 'test-1'), 'Test?'));
    }

    public function testGetDateFormatted()
    {
        $u = new Utilities();
        $format = 'Y-m-d_H:i';
        $this->assertEquals(date($format) ,$u->getDateFormatted($format));
    }
}
