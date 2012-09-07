<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:pmd="http://pmd.sf.net/ruleset/1.0.0"
                version="1.0">

    <xsl:output method="text" />

    <xsl:variable name="nl">
        <xsl:text>
</xsl:text>
    </xsl:variable>
    <xsl:variable name="column.name.length" select="35" />
    <xsl:variable name="column.value.length" select="15" />

    <xsl:template match="/">
        <!--
        <xsl:apply-templates select="*" />
        -->
        <xsl:apply-templates select="pmd:ruleset" />
    </xsl:template>

    <xsl:template match="pmd:ruleset">
        <xsl:call-template name="title.line">
            <xsl:with-param name="text" select="@name" />
        </xsl:call-template>
        <xsl:value-of select="@name" />
        <xsl:value-of select="$nl" />
        <xsl:call-template name="title.line">
            <xsl:with-param name="text" select="@name" />
        </xsl:call-template>
        <xsl:value-of select="$nl" />
        <xsl:apply-templates select="pmd:description" />
        <xsl:apply-templates select="pmd:rule" />
        <xsl:value-of select="$nl" />
            
        <xsl:text>
Remark
======

  This document is based on a ruleset xml-file, that was taken from the original source of the `PMD`__ project. This means that most parts of the content on this page are the intellectual work of the PMD community and its contributors and not of the PHPMD project.

__ http://pmd.sourceforge.net/
        </xsl:text>
    </xsl:template>

    <xsl:template match="pmd:description">
        <xsl:value-of select="normalize-space(text())" />
        <xsl:value-of select="$nl" />
    </xsl:template>

    <xsl:template match="pmd:rule">
        <xsl:value-of select="$nl" />
        <xsl:value-of select="@name" />
        <xsl:value-of select="$nl" />
        <xsl:call-template name="title.line">
            <xsl:with-param name="text" select="@name" />
        </xsl:call-template>
        <xsl:value-of select="$nl" />
        <xsl:text>Since: PHPMD </xsl:text>
        <xsl:value-of select="@since" />
        <xsl:value-of select="$nl" />
        <xsl:value-of select="$nl" />
        <xsl:apply-templates select="pmd:description" />
        <xsl:apply-templates select="pmd:example" />
        <xsl:apply-templates select="pmd:properties" />
    </xsl:template>

    <xsl:template match="pmd:example">
        <xsl:value-of select="$nl" />
        <xsl:value-of select="$nl" />
        <xsl:if test="text() != ''">
        <xsl:text>Example: ::</xsl:text>
        <xsl:value-of select="$nl" />
        <xsl:value-of select="$nl" />
        <xsl:call-template name="indent">
            <xsl:with-param name="text">
                <xsl:call-template name="trim">
                    <xsl:with-param name="text" select=".//text()" />
                </xsl:call-template>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:value-of select="$nl" />
        </xsl:if>
    </xsl:template>

    <xsl:template match="pmd:properties">
        <xsl:if test="pmd:property">
            <xsl:variable name="length">
                <xsl:call-template name="max.description.length" />
            </xsl:variable>

            <xsl:value-of select="$nl" />
            <xsl:text>This rule has the following properties:</xsl:text>
            <xsl:value-of select="$nl" />
            <xsl:value-of select="$nl" />
            <xsl:call-template name="property.table.columns" />
            <xsl:call-template name="pad.right">
                <xsl:with-param name="text" select="'Name'" />
                <xsl:with-param name="length" select="$column.name.length" />
            </xsl:call-template>
            <xsl:text> </xsl:text>
            <xsl:call-template name="pad.right">
                <xsl:with-param name="text" select="'Default Value'" />
                <xsl:with-param name="length" select="$column.value.length" />
            </xsl:call-template>
            <xsl:text> </xsl:text>
            <xsl:call-template name="pad.right">
                <xsl:with-param name="text" select="'Description'" />
                <xsl:with-param name="length" select="$length" />
            </xsl:call-template>
            <xsl:value-of select="$nl" />
            <xsl:call-template name="property.table.columns" />
            <xsl:for-each select="pmd:property">
                <xsl:call-template name="pad.right">
                    <xsl:with-param name="text" select="@name" />
                    <xsl:with-param name="length" select="$column.name.length" />
                </xsl:call-template>
                <xsl:text> </xsl:text>
                <xsl:call-template name="pad.right">
                    <xsl:with-param name="text" select="@value" />
                    <xsl:with-param name="length" select="$column.value.length" />
                </xsl:call-template>
                <xsl:text> </xsl:text>
                <xsl:call-template name="pad.right">
                    <xsl:with-param name="text" select="@description" />
                    <xsl:with-param name="length" select="$length" />
                </xsl:call-template>
                <xsl:value-of select="$nl" />
            </xsl:for-each>
            <xsl:call-template name="property.table.columns" />
        </xsl:if>
    </xsl:template>

    <xsl:template name="property.table.columns">
        <xsl:call-template name="line.char">
            <xsl:with-param name="length" select="$column.name.length" />
        </xsl:call-template>
        <xsl:text> </xsl:text>
        <xsl:call-template name="line.char">
            <xsl:with-param name="length" select="$column.value.length" />
        </xsl:call-template>
        <xsl:text> </xsl:text>
        <xsl:call-template name="line.char">
            <xsl:with-param name="length">
                <xsl:call-template name="max.description.length" />
            </xsl:with-param>
        </xsl:call-template>
        <xsl:value-of select="$nl" />
    </xsl:template>

    <xsl:template name="max.description.length">
        <xsl:for-each select="pmd:property/@description">
            <xsl:sort select="string-length(.)" order="descending" />
            <xsl:if test="position() = 1">
                <xsl:value-of select="string-length(.) + 2" />
            </xsl:if>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="pad.right">
        <xsl:param name="text" />
        <xsl:param name="length" />
        <xsl:param name="offset" select="0" />

        <xsl:if test="$offset = 0">
            <xsl:text> </xsl:text>
            <xsl:value-of select="$text" />
        </xsl:if>
        <xsl:if test="(string-length($text) + 1 + $offset) &lt; $length">
            <xsl:text> </xsl:text>
            <xsl:call-template name="pad.right">
                <xsl:with-param name="text" select="$text" />
                <xsl:with-param name="length" select="$length" />
                <xsl:with-param name="offset" select="$offset + 1" />
            </xsl:call-template>
        </xsl:if>
    </xsl:template>

    <xsl:template name="title.line">
        <xsl:param name="text" />
        <xsl:param name="char" select="'='" />

        <xsl:call-template name="line">
            <xsl:with-param name="text" select="$text" />
            <xsl:with-param name="char" select="$char" />
        </xsl:call-template>
        <xsl:value-of select="$nl" />
    </xsl:template>

    <xsl:template name="line">
        <xsl:param name="text" />
        <xsl:param name="char" select="'='" />

        <xsl:call-template name="line.char">
            <xsl:with-param name="length" select="string-length($text)" />
            <xsl:with-param name="char" select="$char" />
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="line.char">
        <xsl:param name="length" />
        <xsl:param name="offset" select="0" />
        <xsl:param name="char" select="'='" />

        <xsl:if test="$offset &lt; $length">
            <xsl:value-of select="$char" />
            <xsl:call-template name="line.char">
                <xsl:with-param name="length" select="$length" />
                <xsl:with-param name="offset" select="$offset + 1" />
                <xsl:with-param name="char" select="$char" />
            </xsl:call-template>
        </xsl:if>
    </xsl:template>

    <xsl:template name="indent">
        <xsl:param name="text" />
        <xsl:param name="indent" select="'  '" />
        <xsl:choose>
            <xsl:when test="contains($text, $nl)">
                <xsl:value-of select="$indent"/>
                <xsl:value-of select="substring-before($text, $nl)"/>
                <xsl:value-of select="$nl" />
                <xsl:call-template name="indent">
                    <xsl:with-param name="text" select="substring-after($text, $nl)"/>
                    <xsl:with-param name="indent" select="$indent"/>
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$indent"/>
                <xsl:value-of select="$text"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="trim">
        <xsl:param name="text" />
        <xsl:call-template name="trim.left">
            <xsl:with-param name="text">
                <xsl:call-template name="trim.right">
                    <xsl:with-param name="text" select="text()" />
                </xsl:call-template>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="trim.left">
        <xsl:param name="text" />

        <xsl:variable name="char" select="substring($text, 1, 1)" />
        <xsl:choose>
            <xsl:when test="$char = ' ' or $char = $nl or $char = '&#xA;'">
                <xsl:call-template name="trim.left">
                    <xsl:with-param name="text" select="substring($text, 2)" />
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$text" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="trim.right">
        <xsl:param name="text" />

        <xsl:variable name="char" select="substring($text, string-length($text), 1)" />
        <xsl:choose>
            <xsl:when test="$char = ' ' or $char = $nl or $char = '&#xA;'">
                <xsl:call-template name="trim.right">
                    <xsl:with-param name="text" select="substring($text, 1, string-length($text) - 1)" />
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$text" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
  
</xsl:stylesheet>
