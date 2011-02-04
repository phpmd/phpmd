<?php
/**
 * Test function with a high cyclomatic complexity
 */
function ccn_function($arg)
{
    switch ($arg) {

    case 1:
        for ($i = 0; $i < 10; ++$i) {
            if ($i % 2 === 0) {
                if ($arg - $i < 0) {
                    echo "foo";
                }
            }
        }
        break;

    case 2:
        while (true) {
            if (time() % 5 === 0 && time() % 2 === 0) {
                break;
            } else if (time() % 7 === 0) {
                $x = true;
                for ($i = 0; $i < 42; ++$i) {
                    $x = $x || true;
                }
                return $x;
            }
            return 23;
        }
        break;
    }
}
?>
