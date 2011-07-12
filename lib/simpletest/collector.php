<?php
/**
 * This file contains the following classes: {@link SimpleCollector}, 
 * {@link SimplePatternCollector}.
 * 
 * @author Travis Swicegood <development@domain51.com>
 * @package SimpleTest
 * @subpackage UnitTester
 * @version $Id: collector.php 424 2006-07-21 02:20:17Z will $
 */

/**
 * The basic collector for {@link GroupTest}
 *
 * @see collect(), GroupTest::collect()
 * @package SimpleTest
 * @subpackage UnitTester
 */
class SimpleCollector {
    
    /**
     * Strips off any kind of slash at the end so as to normalise the path
     *
     * @param string $path    Path to normalise.
     */
    function _removeTrailingSlash($path) {
        return preg_replace('|[\\/]$|', '', $path);
        
       /**
        * @internal
        * Try benchmarking the following.  It's more code, but by not using the
        * regex, it may be faster?  Also, shouldn't be looking for 
        * DIRECTORY_SEPERATOR instead of a manual "/"? 
        */
        if (substr($path, -1) == DIRECTORY_SEPERATOR) {
            return substr($path, 0, -1);
        } else {
            return $path;
        }
    }

    /**
     * Scans the directory and adds what it can.
     * @param object $test    Group test with {@link GroupTest::addTestFile()} method.
     * @param string $path    Directory to scan.
     * @see _attemptToAdd()
     */
    function collect(&$test, $path) {
        $path = $this->_removeTrailingSlash($path);
        if ($handle = opendir($path)) {
            while (($entry = readdir($handle)) !== false) {
                $this->_handle($test, $path . DIRECTORY_SEPARATOR . $entry);
            }
            closedir($handle);
        }
    }
    
    /**
     * This method determines what should be done with a given file and adds
     * it via {@link GroupTest::addTestFile()} if necessary.
     *
     * This method should be overriden to provide custom matching criteria, 
     * such as pattern matching, recursive matching, etc.  For an example, see
     * {@link SimplePatternCollector::_handle()}.
     *
     * @param object $test      Group test with {@link GroupTest::addTestFile()} method.
     * @param string $filename  A filename as generated by {@link collect()}
     * @see collect()
     * @access protected
     */
    function _handle(&$test, $file) {
        if (!is_dir($file)) {
            $test->addTestFile($file);
        }
    }
}

/**
 * An extension to {@link SimpleCollector} that only adds files matching a
 * given pattern.
 *
 * @package SimpleTest
 * @subpackage UnitTester
 * @see SimpleCollector
 */
class SimplePatternCollector extends SimpleCollector {
    var $_pattern;
    
    
    /**
     *
     * @param string $pattern   Perl compatible regex to test name against
     *  See {@link http://us4.php.net/manual/en/reference.pcre.pattern.syntax.php PHP's PCRE}
     *  for full documentation of valid pattern.s
     */
    function SimplePatternCollector($pattern = '/php$/i') {
        $this->_pattern = $pattern;
    }
    
    
    /**
     * Attempts to add files that match a given pattern.
     *
     * @see SimpleCollector::_handle()
     * @param object $test    Group test with {@link GroupTest::addTestFile()} method.
     * @param string $path    Directory to scan.
     * @access protected
     */
    function _handle(&$test, $filename) {
        if (preg_match($this->_pattern, $filename)) {
            parent::_handle($test, $filename);
        }
    }
}
?>