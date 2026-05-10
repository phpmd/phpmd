<?php

namespace PHPMD\Renderer;

use PHPMD\Report;

/**
 * This class will render a Java-checkstyle compatible xml-report.
 * for use with cs2pr and others
 */
final class CheckStyleRenderer extends XMLRenderer
{
    /**
     * Temporary property that holds the name of the last rendered file, it is
     * used to detect the next processed file.
     */
    private ?string $fileName = null;

    /**
     * Get a violation severity level according to the priority
     * of the rule that's being broken
     *
     * @param int $priority priority of the broken rule
     * @return string either error, warning or info
     * @see https://checkstyle.sourceforge.io/version/4.4/property_types.html#severity
     * - priority 1 maps to error level severity
     * - priority 2 maps to warning level severity
     * - priority > 2 maps to info level severity
     */
    private function mapPriorityToSeverity(int $priority): string
    {
        if ($priority > 2) {
            return 'info';
        }

        return $priority === 2 ? 'warning' : 'error';
    }

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     */
    public function renderReport(Report $report): void
    {
        $writer = $this->getWriter();
        $writer->write('<checkstyle>');
        $writer->write(\PHP_EOL);

        foreach ($report->getRuleViolations() as $violation) {
            $fileName = $violation->getFileName() ?: '(unknown file)';

            if ($this->fileName !== $fileName) {
                // Not first file
                if (null !== $this->fileName) {
                    $writer->write('  </file>' . \PHP_EOL);
                }
                // Store current file name
                $this->fileName = $fileName;

                $writer->write('  <file name="' . $fileName . '">' . \PHP_EOL);
            }

            $rule = $violation->getRule();

            $writer->write('    <error');
            $writer->write(' line="' . $violation->getBeginLine() . '"');
            $writer->write(' endline="' . $violation->getEndLine() . '"');
            $writer->write(\sprintf(' severity="%s"', $this->mapPriorityToSeverity($rule->getPriority())));
            $writer->write(\sprintf(
                ' message="%s (%s, %s) "',
                \htmlspecialchars($violation->getDescription()),
                $rule->getName(),
                $rule->getRuleSetName()
            ));

            $this->maybeAdd('package', $violation->getNamespaceName());
            $this->maybeAdd('externalInfoUrl', $rule->getExternalInfoUrl());
            $this->maybeAdd('function', $violation->getFunctionName());
            $this->maybeAdd('class', $violation->getClassName());
            $this->maybeAdd('method', $violation->getMethodName());
            // $this->_maybeAdd('variable', $violation->getVariableName());

            $writer->write(' />' . \PHP_EOL);
        }

        // Last file and at least one violation
        if (null !== $this->fileName) {
            $writer->write('  </file>' . \PHP_EOL);
        }

        foreach ($report->getErrors() as $error) {
            $writer->write('  <file name="' . $error->getFile() . '">');
            $writer->write($error->getFile());
            $writer->write('<error  msg="');
            $writer->write(\htmlspecialchars($error->getMessage()));
            $writer->write(' severity="error" />' . \PHP_EOL);
        }

        $writer->write('</checkstyle>' . \PHP_EOL);
    }
}
