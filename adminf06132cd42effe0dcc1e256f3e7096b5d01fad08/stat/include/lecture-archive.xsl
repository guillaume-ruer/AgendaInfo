<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 
<xsl:output method="html"/> 

<xsl:template match="annee" >
	<p>Archive de <xsl:value-of select="." />.</p>
</xsl:template>

<xsl:template match="nbevent" >
	<p>Nombre totale d'événements : <xsl:value-of select="." />.</p>
</xsl:template>

<xsl:template match="tab">  
	<p><a href="#" >Revenir en haut.</a></p>

	<table id="{@ancre}" >
		<caption><xsl:value-of select="nom" /></caption>
		<xsl:if test="nc" >
			<tr>
				<xsl:apply-templates select="nc" />	
			</tr>
		</xsl:if>
		<xsl:apply-templates select="ent" />
	</table>
</xsl:template> 

<xsl:template match="nc" >
	<th><xsl:value-of select="." /></th>
</xsl:template>

<xsl:template match="ent" >
	<tr>
		<xsl:apply-templates select="ch" />
	</tr>
</xsl:template>

<xsl:template match="ch" >
	<td><xsl:value-of select="." /></td>
</xsl:template>

</xsl:stylesheet>

