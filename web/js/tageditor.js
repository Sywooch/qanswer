function bindTagFilterAutoComplete(input, baseUrl) {
	baseUrl = baseUrl || iAsk.options.site.baseUrl;
	var url = (baseUrl || "") + "/filter/tags";
	$(input).autocomplete({
        source: url,
		minLength: 1
	});
}
function initTagPreview(g, c, d) {
	var b = null;
	var f;
	var e = window.tagRenderer;
	if (!e) {
		return
	}
	var a = function() {
		if (g.closest("body").length == 0) {
			clearInterval(f);
			return
		}
		var k;
		if (g.hasClass("edit-field-overlayed")) {
			k = ""
		} else {
			k = g.val()
		}
		if (k == b) {
			return
		}
		b = k;
		var l = sanitizeAndSplitTags(k);
		if (l.length == 0) {
			c.slideUp(function() {
				c.empty()
			});
			return
		}
		c.empty();
		for (var j = 0; j < l.length; j++) {
			var h = l[j];
			e(h).appendTo(c);
			c.append("<span> </span>")
		}
		if (l.length > d) {
			c.append("<span class='form-error'>a question cannot have more than " + d + " tags</span>")
		}
		if (!c.is(":visible")) {
			c.slideDown(function() {
				$(".ac_results").css("top", g.offset().top + g[0].offsetHeight)
			})
		}
	};
	f = setInterval(a, 500);
	g.keydown(function(h) {
		if (f) {
			clearInterval(f)
		}
		if (h.which == 32 || h.which == 13) {
			a()
		}
		f = setInterval(a, 500)
	})
}