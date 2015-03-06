<style>
.hi {
    background-color: #CCCCCC;
}
.spaces {
    background-color: #FFFFCC;
}
h1.temphack {
    margin-top: 1em;
}
.col-section {
    padding-bottom: 0.5em;
}
.col-more {
    color: #818185;
    display: inline-block;
    overflow-x: hidden;
    overflow-y: hidden;
}
.col-section {
    width: 600px;
}
h2 > .expander-arrow-small-hide {
    margin-left: 5px;
}
#toc li {
    font-size: 80%;
    margin-bottom: 2px;
}
#toc ul {
    list-style-type: none;
    margin-left: 0;
}

.col-section > * {
    display: none;
}
.col-section > .col-summary, .col-summary.only, .col-section > h2 {
    display: block;
}
.col-section > h2 {
    cursor: pointer;
}
</style>
<script type="text/javascript">
	// Considers each div.col-section a collapsible section, that can be expanded/collapsed by clicking
	// the H2 in this section.
	// - .col-summary.only is only visble in collapsed state
	// - .col-summary and h2 are always visible
	// - everything else is only visible in expanded state
	function initializeCollapsible() {
		// wraps the elements in div and calls .slideDown on that div.
		// expects the elements to be direct neighbors in the DOM.
		function wrapSlideDown(jElems) {
			var wrapper = jElems.wrapAll("<div />").parent().eq(0).hide();
			jElems.css("display", "block");
			wrapper.slideDown(function() {
				jElems.unwrap();
			});
		}
		function wrapSlideUp(jElem) {
			var wrapper = jElem.wrapAll("<div />").parent().eq(0).show();
			wrapper.slideUp(function() {
				jElem.css("display", "none").unwrap();
			});
		}
		function expandCollapse(section, expand, quick) {
			var containers = {
				show: [],
				hide: [],
				leaveAlone: []
			},
			current;
			function add(elem, container) {
				var j;
				if (container !== current) {
					current = container;
					containers[container].push(elem);
				} else {
					j = containers[container].splice( - 1, 1)[0].add(elem);
					containers[container].push(j);
				}
			}
			// group neighboring elements receiving the same "treatment" showing/hiding
			// so they can be wrapped and animated at once, instead of sliding down
			// each by itself (which looks hideous).
			if (expand) {
				section.find("> *").each(function() {
					if ($(this).hasClass("only")) {
						add($(this), "hide")
					} else {
						if ($(this).is(":visible")) add($(this), "leaveAlone")
						else add($(this), "show");
					}
				});
			} else {
				section.find("> *").each(function() {
					if ($(this).is("h2, .col-summary")) {
						if (!$(this).is(":visible")) add($(this), "show")
						else add($(this), "leaveAlone")
					} else {
						add($(this), "hide");
					}
				});
			}
			$.each(containers.show,
			function(i, e) {
				quick ? e.show() : wrapSlideDown(e);
			});
			$.each(containers.hide,
			function(i, e) {
				quick ? e.hide() : wrapSlideUp(e);
			});
		}
		function expand(section, quick) {
			expandCollapse(section, true, quick);
			section.addClass("expanded");
			removeMore.call(section);
		}
		function collapse(section) {
			expandCollapse(section, false);
			section.removeClass("expanded");
		}
		function removeMore() {
			$(this).find(".col-more").animate({
				width: 0
			},
			function() {
				$(this).remove();
			});
		}
		var tocHeader = $("h1:first").text() || "Contents";
		var tocDiv = $("<div class='module newuser' id='toc'><h2 /></div>").appendTo("#scroller").find("h2").text(tocHeader).end();
		var toc = $("<ul />").appendTo(tocDiv);
		function tocClickHandler() {
			var h2 = $($(this).attr("href")),
			section = h2.closest(".col-section");
			if (!section.hasClass("expanded")) {
				section.find(".expander-arrow-small-hide").toggleClass("expander-arrow-small-show");
				expand(section, true);
			}
		}
		function addToToc(h2elem) {
			var jElem = $(h2elem);
			var id = jElem.attr("id") || jElem.attr("name") || $.trim(jElem.text()).toLowerCase().replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, "");
			$('<a href="#' + id + '" />').text(jElem.text()).appendTo($("<li />").appendTo(toc)).click(tocClickHandler);
			jElem.attr("id", id);
		}
		$(".col-section > h2").click(function() {
			var section = $(this).closest(".col-section");
			$(this).find(".expander-arrow-small-hide").toggleClass("expander-arrow-small-show");
			if (section.hasClass("expanded")) {
				collapse(section);
			} else {
				expand(section);
			}
		}).each(function() {
			$("<span class='expander-arrow-small-hide' />").appendTo($(this).attr("title", "点击展开/折叠本节"));
			addToToc(this);
		});
//		$(".col-section").delayedHover(function() {
//			if ($(this).find(".col-more").length > 0 || $(this).hasClass("expanded")) return;
//			var outer = $("<span class='col-more' style='width:0px' />").insertBefore($(this).find(".expander-arrow-small-hide"));
//			var inner = $("<span>&nbsp;more&hellip;</span>").appendTo(outer);
//			outer.animate({
//				width: inner.outerWidth(true)
//			});
//		},
//		removeMore, 500, 50);
	}
	$(initializeCollapsible);
	$(function() {
		moveScroller();
	});
</script>
<div class="subheader">
	<h1>Markdown帮助</h1>
</div>
<div id="mainbar">
	<div class="content-page">

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="code-and-preformatted-text">代码和预格式化文本</h2>
    <p>在代码的每一行缩进四个空格以产生<code>&lt;pre&gt;</code><code>&lt;code&gt;</code>块，例如：</p>
    <pre class="col-summary"><span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>printf("goodbye world!");  /* his suicide note
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>                              was in C */
</pre>
    <p>文本将会包在标签里，以等宽字体显示。最前面的四个空格会被自动去掉，但是其他的空白将会被保留。</p>
    <p>Markdown和HTML在代码块中会被忽略，例如：</p>
    <pre><span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>&lt;blink&gt;
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>   You would hate this if it weren't
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>   wrapped in a code block.
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>&lt;/blink&gt;
</pre>
    </div>

    <div class="col-section">
	    <h2 title="点击展开/折叠本节" id="code-spans">行内代码</h2>
	    <p>如果打算在一段文字中插入一句代码，可以使用反引号`创建<code>&lt;code&gt;</code> 扩展，例如：</p>
	    <pre class="col-summary">Press the <span class="hi">`</span>&lt;Tab&gt;<span class="hi">`</span> key, then type a <span class="hi">`</span>$<span class="hi">`</span>.</pre>
	    <p>(反引号`键在键盘的左上角，数字1键的左侧。)</p>
	    <p>和代码块类似，代码扩展以等宽字体显示。Markdown和HTML在代码扩展中会被忽略。</p>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="linebreaks">换行</h2>
    <p>在行末加两个空格，产生<code>&lt;br/&gt;</code> 换行，例如：
        End a line with two spaces to add a <code>&lt;br/&gt;</code> linebreak:</p>
    <pre class="col-summary">How do I love thee?<span class="spaces">&nbsp;&nbsp;</span>
Let me count the ways
</pre>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="italics-and-bold">斜体和粗体</h2>
    <pre class="col-summary only"><span class="hi">*</span>This is italicized<span class="hi">*</span>, <span class="hi">**</span>this is bold<span class="hi">**</span>.
</pre>

    <pre><span class="hi">*</span>This is italicized<span class="hi">*</span>, and so is <span class="hi">_</span>this<span class="hi">_</span>.
<span class="hi">**</span>This is bold<span class="hi">**</span>, and so is <span class="hi">__</span>this<span class="hi">__</span>.
Use <span class="hi">***</span>italics and bold together<span class="hi">***</span> if you <span class="hi">___</span>have to<span class="hi">___</span>.
</pre>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="basic-links">基本链接</h2>
    <p>有三个方法添加链接：</p>
    <pre class="col-summary only">内嵌链接 <span class="hi">[Google](http://www.google.com/)</span>.
</pre>

    <pre>内嵌链接 <span class="hi">[Google](http://www.google.com/)</span>.
引用风格链接 <span class="hi">[Google][1]</span>.
易读格式链接 to <span class="hi">[Yahoo!][yahoo]</span>.

  <span class="hi">[1]:</span> http://www.google.com/
  <span class="hi">[yahoo]:</span> http://www.yahoo.com/
</pre>
    <p>
        链接定义可以出现在文档的任何地方 - 而不必一定在使用前定义. 链接定义名称 <code>[1]</code> 和 <code>[yahoo]</code>
        可以是任何唯一的字符串, 且不区分大小写; <code>[yahoo]</code> 和 <code>[YAHOO]</code>一样。</p>
    </div>

	<div class="col-section">
		<h2 title="点击展开/折叠本节" id="advanced-links">高级链接</h2>
		<p>链接可以有"title"属性。如果链接本身没有描述清楚，"title"属性可以帮助描述链接的意思。</p>
		<pre class="col-summary only">访问 <span class="hi">[我们][web]</span>.

  <span class="hi">[web]:</span> http://ilewen.com/ "乐问"
</pre>
    <pre>这是一个 <span class="hi">[好网站](http://ilewen.com/ "ilewen")</span>.
不要写成 "<span class="hi">[click here][^2]</span>".
访问 <span class="hi">[us][web]</span>.

  <span class="hi">[^2]:</span> http://www.w3.org/QA/Tips/noClickHere
        (Advice against the phrase "click here")
  <span class="hi">[web]:</span> http://ilewen.com/ "乐问"
</pre>
    <p>你也可以使用标准的HTML超链接语法：</p>
    <pre>&lt;a href="http://example.com" title="example"&gt;example&lt;/a&gt;
</pre>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="headers">标题</h2>
    <p>对文本添加下划线，可以使其变为标题，共两级<code>&lt;h1&gt;</code> <code>&lt;h2&gt;</code>：</p>
    <pre>Header 1
<span class="hi">========</span>

Header 2
<span class="hi">--------</span>
</pre>
    <p>=或-的数目无关紧要，但是一般为了美观，我们尽量将其和文本对齐。</p>
    <p>使用#可以产生更多级的标题：</p>
    <pre class="col-summary only"><span class="hi">#</span> Header 1 <span class="hi">#</span>
<span class="hi">##</span> Header 2 <span class="hi">##</span>
</pre>
    <pre><span class="hi">#</span> Header 1 <span class="hi">#</span>
<span class="hi">##</span> Header 2 <span class="hi">##</span>
<span class="hi">###</span> Header 3 <span class="hi">###</span>
</pre>
    <p>结尾的 # 字符可选</p>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="horizontal-rules">分割线</h2>
    <p>使用超过三个或跟多的-，*，或_字符可以产生水平分割线（<code>&lt;hr/&gt;</code>)</p>
    <pre class="col-summary only"><span class="hi">---</span>
</pre>
    <pre>规则 #1

<span class="hi">---</span>

规则 #2

<span class="hi">*******</span>

规则 #3

<span class="hi">___</span>

</pre>
    <p>在字符之间使用空格也可以，例如：:</p>
    <pre>规则 #4

<span class="hi">- - - -</span>

</pre>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="simple-lists">基本列表</h2>
    <p>项目列表<code>&lt;ul&gt;</code></p>
    <pre class="col-summary"><span class="hi">-</span><span class="spaces">&nbsp;</span>用减号表示列表
<span class="hi">+</span><span class="spaces">&nbsp;</span>或者加号
<span class="hi">*</span><span class="spaces">&nbsp;</span>或者乘号
</pre>
    <p>
        数字编号 <code>&lt;ol&gt;</code> 列表:</p>
    <pre class="col-summary"><span class="hi">1.</span><span class="spaces">&nbsp;</span>数字列表非常简单
<span class="hi">2.</span><span class="spaces">&nbsp;</span>Markdown将自动更新列表的数字编号
<span class="hi">7.</span><span class="spaces">&nbsp;</span>因此该条目的编号为3
</pre>
    <p>双间距列表:</p>
    <pre><span class="hi">-</span><span class="spaces">&nbsp;</span>该列表被 &lt;p&gt; 标签包装
<span class="spaces">&nbsp;</span>
<span class="hi">-</span><span class="spaces">&nbsp;</span>因此在列表之间有格外的间距
</pre>
    </div>

    <div class="col-section">
		<h2 title="点击展开/折叠本节" id="advanced-lists-nesting">高级列表：多级列表</h2>
    <p>通过每级列表行缩进四个空格，可以产生多级列表，例如：</p>
    <pre class="col-summary only"><span class="hi">1.</span><span class="spaces">&nbsp;</span>Lists in a list item:
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="hi">-</span><span class="spaces">&nbsp;</span>Indented four spaces.
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="hi">*</span><span class="spaces">&nbsp;</span>indented eight spaces.
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="hi">-</span><span class="spaces">&nbsp;</span>Four spaces again.
</pre>

    <pre><span class="hi">1.</span><span class="spaces">&nbsp;</span>Lists in a list item:
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="hi">-</span><span class="spaces">&nbsp;</span>Indented four spaces.
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="hi">*</span><span class="spaces">&nbsp;</span>indented eight spaces.
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="hi">-</span><span class="spaces">&nbsp;</span>Four spaces again.
<span class="hi">2.</span><span class="spaces">&nbsp;&nbsp;</span>Multiple paragraphs in a list items:
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>It's best to indent the paragraphs four spaces
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>You can get away with three, but it can get
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>confusing when you nest other things.
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>Stick to four.
<span class="spaces">&nbsp;</span>
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>We indented the first line an extra space to align
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>it with these paragraphs.  In real use, we might do
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>that to the entire list so that all items line up.
<span class="spaces">&nbsp;</span>
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span>This paragraph is still part of the list item, but it looks messy to humans.  So it's a good idea to wrap your nested paragraphs manually, as we did with the first two.
<span class="spaces">&nbsp;</span>
<span class="hi">3.</span><span class="spaces">&nbsp;</span>Blockquotes in a list item:
<span class="spaces"> </span>
<span class="spaces"></span><span class="hi">&gt;</span> Skip a line and
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="hi">&gt;</span> indent the &gt;'s four spaces.
<span class="spaces">&nbsp;</span>
<span class="hi">4.</span><span class="spaces">&nbsp;</span>Preformatted text in a list item:
<span class="spaces">&nbsp;</span>
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>Skip a line and indent eight spaces.
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>That's four spaces for the list
<span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>and four to trigger the code block.
</pre>
    </div>

	<div class="col-section">
		<h2 title="点击展开/折叠本节" id="simple-blockquotes">简单块引用(Blockquote)</h2>
    <p>在任意行的开始添加<code>&gt;</code>字符产生<code>&lt;blockquote&gt;</code>，例如：</p>
    <pre class="col-summary only"><span class="hi">&gt;</span> The syntax is based on the way email programs
<span class="hi">&gt;</span> usually do quotations.
</pre>
    <pre><span class="hi">&gt;</span> The syntax is based on the way email programs
<span class="hi">&gt;</span> usually do quotations. You don't need to hard-wrap
<span class="hi">&gt;</span> the paragraphs in your blockquotes, but it looks much nicer if you do.  Depends how lazy you feel.
</pre>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="advanced-blockquotes-nesting">高级块引用：多级引用</h2>
    <p>要在<code>&lt;blockquote&gt;</code>中添加另一个Markdown块，只需要在空格后加一个<code>&gt;</code>字符，例如：</p>
    <pre><span class="hi">&gt;</span> The &gt; on the blank lines is optional.
<span class="hi">&gt;</span> Include it or don't; Markdown doesn't care.
<span class="hi">&gt;</span><span class="spaces">&nbsp;</span>
<span class="hi">&gt;</span> But your plain text looks better to
<span class="hi">&gt;</span> humans if you include the extra `&gt;`
<span class="hi">&gt;</span> between paragraphs.
</pre>
    <p>块引用嵌套：:</p>
    <pre><span class="hi">&gt;</span> A standard blockquote is indented
<span class="hi">&gt;</span> <span class="hi">&gt;</span> A nested blockquote is indented more
<span class="hi">&gt;</span> <span class="hi">&gt;</span> <span class="hi">&gt;</span> <span class="hi">&gt;</span> You can nest to any depth.
</pre>
    <p>在块引用中添加列表：</p>
    <pre><span class="hi">&gt;</span><span class="spaces">&nbsp;</span><span class="hi">-</span> A list in a blockquote
<span class="hi">&gt;</span><span class="spaces">&nbsp;</span><span class="hi">-</span> With a &gt; and space in front of it
<span class="hi">&gt;</span><span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="hi">*</span> A sublist
</pre>
    <p>在块引用中添加预格式文本：</p>
    <pre><span class="hi">&gt;</span><span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>Indent five spaces total.  The first
<span class="hi">&gt;</span><span class="spaces">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>one is part of the blockquote designator.
</pre>
    </div>

	<div class="col-section">
		<h2 title="点击展开/折叠本节" id="images">图片</h2>
		<p>图片和链接很类似，但是需要在它们之前添加一个声明：</p>
    <pre class="col-summary">![Valid XHTML](http://w3.org/Icons/valid-xhtml10).
</pre>
    <p>方括弧中的词表示alt属性，如果浏览器不能找到图片，那么将会显示中该词。所以要确保这些单词是有意义，以使得屏幕阅读器可以识别它。</p>
    <p>和链接一样，图片也可以使用引用语法和标题属性：</p>
    <pre>This page is <span class="hi">![valid XHTML][checkmark]</span>.

<span class="hi">[checkmark]:</span> http://w3.org/Icons/valid-xhtml10
             "What are you smiling at?"
</pre>
    <p>注意：Markdown目前不支持对图片的简写引用语法，例如：</p>
    <pre>Here's a broken ![checkmark].
</pre>
    <p>但是你可以使用一个稍微冗长的引用版本</p>
    <pre>This <span class="hi">![checkmark][]</span> works.
</pre>
    <p>引用名称同时也作为"alt"属性</p>
    <p>你也可以使用标准的HTML图片语法，并可以设置宽高属性：</p>
    <pre>&lt;img src="http://example.com/sample.png" width="100" height="100"&gt;
</pre>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="inline-html">内嵌HTML</h2>
    <p>如果你遇到了Markdown无法处理的情况，那么可以使用HTML，注意我们仅仅支持HTML的一个精简集！</p>
    <pre class="col-summary"> Strikethrough humor is <span class="hi">&lt;strike&gt;</span>funny<span class="hi">&lt;/strike&gt;</span>.
</pre>
    <p>Markdown不能处理行内HTML元素，例如：</p>
    <pre><span class="hi">&lt;u&gt;</span>Markdown works <span class="hi">*</span>fine<span class="hi">*</span> in here.<span class="hi">&lt;/u&gt;</span>
</pre>
    <p>块级HTML元素有一些限制：:</p>
    <ol>
        <li>They must be separated from surrounding text by blank lines.</li>
        <li>最外层块元素的开始和结尾标志不能缩进</li>
        <li>Markdown不能在HTML块内部中使用</li>
    </ol>
    <pre><span class="hi">&lt;pre&gt;</span>
    You can <span class="hi">&lt;em&gt;</span>not<span class="hi">&lt;/em&gt;</span> use Markdown in here.
<span class="hi">&lt;/pre&gt;</span>
</pre>
    </div>

    <div class="col-section">
    <h2 title="点击展开/折叠本节" id="need-more-detail">需要了解更多信息？</h2>
    <p>请访问<a target="_blank" href="http://daringfireball.net/projects/markdown/syntax">Markdown语法官方指南</a>.</p>
    </div>



    </div>
	<div class="cbt"></div>
</div>
<div id="sidebar">
    <div id="scroller-anchor"></div>
    <div id="scroller" style="position: relative; width: 220px;">
	</div>
</div>