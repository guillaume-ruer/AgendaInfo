<?xml version="1.0"?> 
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 
<xsl:output method="html"/> 
<xsl:template match="/">  
	<xsl:for-each select="tout/element[theme/@num=$idtheme or $idtheme='']">  
		
		<xsl:sort data-type="text" select="date" order="ascending"/>  
		<xsl:sort data-type="number" select="@id" order="descending"/>  
			<tr> 
				<td><xsl:value-of select="@id" /></td>
				<td><xsl:value-of select="date" /></td>
				<td><xsl:value-of select="titre"/></td>  
				<td><xsl:value-of select="description"/></td>  
				<td><xsl:value-of select="theme"/></td>  
			</tr>  
	</xsl:for-each>  
</xsl:template> 
</xsl:stylesheet>
