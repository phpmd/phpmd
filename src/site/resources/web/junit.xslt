<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="xml" indent="yes" encoding="utf-8"/>

    <xsl:template match="/pmd">
        <testsuites failures="{count(./file/violation)}" errors="{count(./error)}">
            <xsl:apply-templates select="file" />
            <xsl:apply-templates select="error" />
        </testsuites>
    </xsl:template>

    <xsl:template match="/pmd/file">
        <testsuite name="{@name}" tests="{count(./violation)}" failures="{count(./violation)}" errors="0">
            <xsl:apply-templates select="violation" />
        </testsuite>
    </xsl:template>

    <xsl:template match="/pmd/error">
        <testsuite name="{@name}" tests="1" failures="0" errors="1">
            <testcase name="error">
                <error message="{@msg}" type="error">
                    <xsl:value-of select="@msg" />
                </error>
            </testcase>
        </testsuite>
    </xsl:template>

    <xsl:template match="/pmd/file/violation">
        <testcase name="{@rule}">
            <failure message="{normalize-space(text())}" type="{@ruleset}">
                <xsl:value-of select="normalize-space(text())" />
                <xsl:text>&#x0A;</xsl:text>
                <xsl:text>Lines: </xsl:text>
                <xsl:value-of select="@beginline" />
                <xsl:text>-</xsl:text>
                <xsl:value-of select="@endline" />
                <xsl:text>&#x0A;</xsl:text>
                <xsl:value-of select="@externalInfoUrl" />
            </failure>
        </testcase>
    </xsl:template>
</xsl:stylesheet>
