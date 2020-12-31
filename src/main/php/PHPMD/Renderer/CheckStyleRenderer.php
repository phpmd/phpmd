<?php

namespace PHPMD\Renderer;


use PHPMD\AbstractRenderer;
use PHPMD\PHPMD;
use PHPMD\Report;

/**
 * This class will render a Java-checkstyle compatible xml-report.
 * for use with cs2pr and others
 */
class CheckStyleRenderer extends AbstractRenderer
{
    /**
     * Temporary property that holds the name of the last rendered file, it is
     * used to detect the next processed file.
     *
     * @var string
     */
    private $fileName;

    /**
     * This method will be called on all renderers before the engine starts the
     * real report processing.
     */
    public function start(): void
    {
        $this->getWriter()->write('<?xml version="1.0" encoding="UTF-8" ?>');
        $this->getWriter()->write(\PHP_EOL);
    }

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     *
     * @param \PHPMD\Report $report
     */
    public function renderReport(Report $report): void
    {
        $writer = $this->getWriter();
        $writer->write('<checkstyle version="3.5.3">');
        $writer->write(\PHP_EOL);

        foreach ($report->getRuleViolations() as $violation) {
            $fileName = \str_replace(__DIR__ . \DIRECTORY_SEPARATOR, '', $violation->getFileName());

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
            $writer->write(\sprintf(' severity="%s"', 2 < $rule->getPriority() ? 'warning' : 'error'));
            $writer->write(\sprintf(' message="%s (%s, %s) "', \htmlspecialchars($violation->getDescription()), $rule->getName(), $rule->getRuleSetName()));

            $this->maybeAdd('package', $violation->getNamespaceName());
            $this->maybeAdd('externalInfoUrl', $rule->getExternalInfoUrl());
            $this->maybeAdd('function', $violation->getFunctionName());
            $this->maybeAdd('class', $violation->getClassName());
            $this->maybeAdd('method', $violation->getMethodName());
            //$this->_maybeAdd('variable', $violation->getVariableName());

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

    /**
     * This method will write a xml attribute named <b>$attr</b> to the output
     * when the given <b>$value</b> is not an empty string and is not <b>null</b>.
     *
     * @param string $attr  the xml attribute name
     * @param string $value the attribute value
     */
    private function maybeAdd($attr, $value): void
    {
        if (null === $value || '' === \trim($value)) {
            return;
        }
        $this->getWriter()->write(' ' . $attr . '="' . $value . '"');
    }
}
