<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:media="http://search.yahoo.com/mrss/">
  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="@langcode">
      <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title><xsl:value-of select="/rss/channel/title"/> — RSS</title>
        <style>
          *,*::before,*::after{box-sizing:border-box}
          body{
            margin:0;
            font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
            line-height:1.6;
            color:#1a1a1a;
            background:#fff;
          }
          .container{max-width:700px;margin:0 auto;padding:2rem 1rem}
          .banner{
            background:#fef3cd;
            border-bottom:1px solid #e9d98a;
            padding:1rem;
            text-align:center;
            font-size:.9rem;
            color:#664d03;
          }
          .banner a{color:#664d03;font-weight:600}
          .site-header{display:flex;align-items:flex-start;gap:1rem}
          .site-logo{width:48px;height:48px;flex-shrink:0}
          h1{margin:0 0 .25rem;font-size:1.5rem;line-height:1}
          .description{color:#555;margin:0}
          .about{color:#555;margin:.75rem 0 1rem;font-size:.95rem}
          .about:empty{display:none}
          .meta{display:flex;align-items:center;gap:.5rem;margin-bottom:2rem}
          .meta a{
            display:inline-block;
            padding:.35rem .75rem;
            background:#f0f0f0;
            border-radius:4px;
            color:#1a1a1a;
            text-decoration:none;
            font-size:.85rem;
          }
          .meta a:hover{background:#e0e0e0}
          h2{font-size:1.1rem;margin:2rem 0 1rem;color:#555;text-transform:uppercase;letter-spacing:.05em}
          .item{
            display:flex;
            gap:1rem;
            padding:1rem 0;
            border-bottom:1px solid #eee;
          }
          .item:last-child{border-bottom:none}
          .item-image{width:120px;height:160px;object-fit:cover;border-radius:4px;flex-shrink:0}
          .item-body{min-width:0}
          .item h3{margin:0 0 .25rem;font-size:1rem}
          .item h3 a{color:#1a1a1a;text-decoration:none}
          .item h3 a:hover{color:#0066cc}
          .item time{font-size:.8rem;color:#888}
          .item p{margin:.5rem 0 0;color:#555;font-size:.9rem}
        </style>
      </head>
      <body>
        <div class="banner">
          @banner_text
          <xsl:text> </xsl:text>
          <a href="https://aboutfeeds.com">@what_is_rss</a>
        </div>
        <div class="container">
          <header>
            <div class="site-header">
              <img class="site-logo" src="@logo_url" alt=""/>
              <div>
                <h1><xsl:value-of select="/rss/channel/title"/></h1>
                <p class="description"><xsl:value-of select="/rss/channel/description"/></p>
              </div>
            </div>
            <p class="about">@about_text</p>
            <div class="meta">
              <a>
                <xsl:attribute name="href">
                  <xsl:value-of select="/rss/channel/link"/>
                </xsl:attribute>
                @visit_site &#x2192;
              </a>
            </div>
          </header>
          <h2>@recent_posts</h2>
          <xsl:for-each select="/rss/channel/item">
            <article class="item">
              <xsl:if test="media:content[@medium='image']">
                <img class="item-image">
                  <xsl:attribute name="src">
                    <xsl:value-of select="media:content[@medium='image']/@url"/>
                  </xsl:attribute>
                  <xsl:attribute name="width">
                    <xsl:value-of select="media:content[@medium='image']/@width"/>
                  </xsl:attribute>
                  <xsl:attribute name="height">
                    <xsl:value-of select="media:content[@medium='image']/@height"/>
                  </xsl:attribute>
                  <xsl:attribute name="alt">
                    <xsl:value-of select="title"/>
                  </xsl:attribute>
                </img>
              </xsl:if>
              <div class="item-body">
                <h3>
                  <a>
                    <xsl:attribute name="href">
                      <xsl:value-of select="link"/>
                    </xsl:attribute>
                    <xsl:value-of select="title"/>
                  </a>
                </h3>
                <time><xsl:value-of select="pubDate"/></time>
                <xsl:if test="description">
                  <p><xsl:value-of select="description"/></p>
                </xsl:if>
              </div>
            </article>
          </xsl:for-each>
        </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
