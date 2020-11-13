[shortrss]<item turbo="{allow-turbo}">
<title>{title}</title>
<guid isPermaLink="true">{rsslink}</guid>
<link>{rsslink}</link>
<description>{short-story}</description>
[allow-turbo]<turbo:content><![CDATA[{full-story}]]></turbo:content>[/allow-turbo]
<category>{category}</category>
<dc:creator>{rssauthor}</dc:creator>
<pubDate>{rssdate}</pubDate>
</item>[/shortrss]
[fullrss]<item turbo="{allow-turbo}">
<title>{title}</title>
<guid isPermaLink="true">{rsslink}</guid>
<link>{rsslink}</link>
<category><![CDATA[{category}]]></category>
<dc:creator>{rssauthor}</dc:creator>
<pubDate>{rssdate}</pubDate>
<description><![CDATA[{short-story}]]></description>
[allow-turbo]<turbo:content><![CDATA[{full-story}]]></turbo:content>[/allow-turbo]
[allow-dzen]<content:encoded><![CDATA[{full-story}]]></content:encoded>[/allow-dzen]
</item>[/fullrss]
[yandexrss]<item turbo="{allow-turbo}">
<title>{title}</title>
<link>{rsslink}</link>
<description>{short-story}</description>
<category>{category}</category>{images}
<pubDate>{rssdate}</pubDate>
<yandex:full-text>{full-story}</yandex:full-text>
[allow-turbo]<turbo:content><![CDATA[{full-story}]]></turbo:content>[/allow-turbo]
[allow-dzen]<content:encoded><![CDATA[{full-story}]]></content:encoded>[/allow-dzen]
</item>[/yandexrss]