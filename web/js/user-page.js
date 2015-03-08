(function ($) {
    var a = $.scrollTo = function (d, f, e) {
        $(window).scrollTo(d, f, e)
    };
    a.defaults = {
        axis: "xy",
        duration: parseFloat($.fn.jquery) >= 1.3 ? 0 : 1
    };
    a.window = function (d) {
        return $(window)._scrollable()
    };
    $.fn._scrollable = function () {
        return this.map(function () {
            var f = this,
                    d = !f.nodeName || $.inArray(f.nodeName.toLowerCase(), ["iframe", "#document", "html", "body"]) != -1;
            if (!d) {
                return f
            }
            var e = (f.contentWindow || f).document || f.ownerDocument || f;
            return e.documentElement;
        })
    };
    $.fn.scrollTo = function (d, f, e) {
        if (typeof f == "object") {
            e = f;
            f = 0
        }
        if (typeof e == "function") {
            e = {
                onAfter: e
            }
        }
        if (d == "max") {
            d = 9000000000
        }
        e = $.extend({},
                a.defaults, e);
        f = f || e.speed || e.duration;
        e.queue = e.queue && e.axis.length > 1;
        if (e.queue) {
            f /= 2
        }
        e.offset = b(e.offset);
        e.over = b(e.over);
        return this._scrollable().each(function () {
            var g = this,
                    h = $(g),
                    l = d,
                    m,
                    i = {},
                    j = h.is("html,body");
            switch (typeof l) {
                case "number":
                case "string":
                    if (/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(l)) {
                        l = b(l);
                        break
                    }
                    l = $(l, this);
                case "object":
                    if (l.is || l.style) {
                        m = (l = $(l)).offset()
                    }
            }
            $.each(e.axis.split(""),
                    function (n, u) {
                        var p = u == "x" ? "Left" : "Top",
                                o = p.toLowerCase(),
                                s = "scroll" + p,
                                r = g[s],
                                t = a.max(g, u);
                        if (m) {
                            i[s] = m[o] + (j ? 0 : r - h.offset()[o]);
                            if (e.margin) {
                                i[s] -= parseInt(l.css("margin" + p)) || 0;
                                i[s] -= parseInt(l.css("border" + p + "Width")) || 0
                            }
                            i[s] += e.offset[o] || 0;
                            if (e.over[o]) {
                                i[s] += l[u == "x" ? "width" : "height"]() * e.over[o]
                            }
                        } else {
                            var q = l[o];
                            i[s] = q.slice && q.slice(-1) == "%" ? parseFloat(q) / 100 * t : q
                        }
                        if (/^\d+$/.test(i[s])) {
                            i[s] = i[s] <= 0 ? 0 : Math.min(i[s], t)
                        }
                        if (!n && e.queue) {
                            if (r != i[s]) {
                                k(e.onAfterFirst)
                            }
                            delete i[s]
                        }
                    });
            k(e.onAfter);
            function k(n) {
                h.animate(i, f, e.easing, n &&
                        function () {
                            n.call(this, d, e)
                        })
            }
        }).end()
    };
    a.max = function (f, j) {
        var e = j == "x" ? "Width" : "Height",
                h = "scroll" + e;
        if (!$(f).is("html,body")) {
            return f[h] - $(f)[e.toLowerCase()]()
        }
        var d = "client" + e,
                i = f.ownerDocument.documentElement,
                g = f.ownerDocument.body;
        return Math.max(i[h], g[h]) - Math.min(i[d], g[d])
    };
    function b(d) {
        return typeof d == "object" ? d : {
            top: d,
            left: d
        }
    }
})(jQuery);

(function ($) {
    var b = {
        put: function (g, h) {
            (h || window).location.hash = encodeURIComponent(g)
        },
        get: function (g) {
            var h = ((g || window).location.hash).replace(/^#/, "");
//			return $.browser.fx ? h: decodeURIComponent(h)
            return decodeURIComponent(h)
        }
    };
    var c = {
        id: "__jQuery_history",
        init: function () {
            var g = '<iframe id="' + this.id + '" style="display:none" src="javascript:false;" />';
            $("body").prepend(g);
            return this;
        },
        _document: function () {
            return $("#" + this.id)[0].contentWindow.document;
        },
        put: function (g) {
            var h = this._document();
            h.open();
            h.close();
            b.put(g, h)
        },
        get: function () {
            return b.get(this._document());
        }
    };
    var _history = {
        appState: undefined,
        callback: undefined,
        init: function (g) {
        },
        check: function () {
        },
        load: function (g) {
        }
    };
    $.history = _history;
    var _historyNormal = {
        init: function (g) {
            _history.callback = g;
            var h = b.get();
            _history.appState = h;
            _history.callback(h);
            setInterval(_history.check, 100)
        },
        check: function () {
            var g = b.get();
            if (g != _history.appState) {
                _history.appState = g;
                _history.callback(g)
            }
        },
        load: function (g) {
            if (g != _history.appState) {
                b.put(g);
                _history.appState = g;
                _history.callback(g)
            }
        }
    };
    var _historyIE = {
        init: function (g) {
            _history.callback = g;
            var h = b.get();
            _history.appState = h;
            c.init().put(h);
            _history.callback(h);
            setInterval(_history.check, 100)
        },
        check: function () {
            var g = c.get();
            if (g != _history.appState) {
                b.put(g);
                _history.appState = g;
                _history.callback(g)
            }
        },
        load: function (g) {
            if (g != _history.appState) {
                b.put(g);
                c.put(g);
                _history.appState = g;
                _history.callback(g)
            }
        }
    };
//	if ($.browser.msie && ($.browser.version < 8 || document.documentMode < 8)) {
//		$.extend(_history, _historyIE)
//	} else {
    $.extend(_history, _historyNormal);
//	}
})(jQuery);

function pageHistory(param) {
    var that = this;
    this.defaults = $.extend({}, param);
    for (att in param) {
        that[att] = param[att]
    }
    this.toUrl = function () {
        var c = [];
        for (att in that) {
            if (att != "defaults" && typeof (that[att]) != "function") {
                c.push(att + "_" + that[att])
            }
        }
        return c.join("-")
    };
    this.parseUrl = function (d) {
        var c = $.extend({}, that.defaults);
        $.each(d.split("-"),
                function () {
                    var e = this.split("_");
                    c[e[0]] = e[1]
                });
        return c
    };
    this.update = function (c) {
        var d = false;
        for (att in c) {
            if (typeof (that[att]) == "undefined") {
                continue
            }
            d |= (c[att] != that[att]);
            that[att] = c[att]
        }
        return d
    }
}
function parsePagedUrl(pageUrl) {
    var result = {},
            i,
//	f = /\+/g,
            h = /([^&=]+)=?([^&]*)/g,
            decodeUrl = function (_url) {
                return decodeURIComponent(_url.replace(/\+/g, " "))
            },
            g = pageUrl.split("?").pop();
    while (i = h.exec(g)) {
        result[decodeUrl(i[1])] = decodeUrl(i[2]);
    }
    return result;
}
function loadUrl(url, id, page, sort, filter, c) {
    if (page > -1) {
        url += "&page=" + page;
    }
    if (sort != null) {
        url += "&sort=" + sort;
    }
    if (filter != null) {
        url += "&filter=" + filter;
    }
    if (typeof userViewStartDate != "undefined" && !!userViewStartDate) {
        url += "&StartDate=" + userViewStartDate;
    }
//	$.post(url,function(data) {
//		$(id).html(data);
//	});
    // xuesong 更改post为get
    $.get(url, function (data) {
        $(id).html(data);
    });
    if (c != true) {
        $.scrollTo(id, 400);
    }
}
function loadReputation(page, sort, a, href) {
    loadUrl(href, "#rep-page-container", page, sort, null, a);
}
function loadActivity(page, filter, a, href) {
    loadUrl(href, "#history-table", page, null, filter, a);
}
function loadResponses(page, filter, a) {
    loadUrl("/users/responses/show?pagesize=" + responsesPageSize + "&uid=" + uid, "#history-table", page, null, filter, a)
}
function loadAnswers(page, sort) {
    loadUrl(iAsk.options.links.userStat + "/?do=answers&pagesize=" + answersPageSize + "&uid=" + uid, "#answers-table", page, sort)
}
function loadQuestions(page, sort, href) {
    loadUrl(href, "#questions-table", page, sort)
}
//function loadTags(page) {
//    $.get(iAsk.options.links.userStat + "/?do=tags&pagesize=" + tagsPageSize + "&uid=" + uid + "&page=" + page, function (data) {
//        $("#tags-table").html(data);
//    })
//}
function loadTags(href) {
    $.get(href, function (data) {
        $("#tags-table").html(data);
    })
}
function loadFavorites(page, sort, href) {
    $.get(href, function (data) {
        $("#favorites-table").html(data);
    });
    if ($(this).closest(".favorite-pager").length > 0) {
        $.scrollTo("#favorites-table", 400);
    }
}
$(function () {
    var pHistory = null;
    if (typeof (questionsSortOrder) != "undefined") {
        pHistory = new pageHistory({
            qpage: 1,
            anpage: 1,
            qsort: questionsSortOrder,
            ansort: answersSortOrder
        })
    }
    if ($("#favorites-table").length > 0) {
        pHistory = new pageHistory({
            fpage: 1,
            fsort: favoritesSortOrder
        })
    }
    if ($(".user-activity-table").length > 0) {
        pHistory = new pageHistory({
            apage: 1,
            afilter: activityFilter
        })
    }
    if ($(".user-responses-table").length > 0) {
        pHistory = new pageHistory({
            rpage: 1,
            rfilter: responsesFilter
        })
    }
    if ($("#rep-page-container").length > 0) {
        pHistory = new pageHistory({
            reppage: 1,
            repview: reputationView
        })
    }
    if (pHistory != null) {
        $.history.init(function (c) {
            var b = pHistory.parseUrl(c);
            if (pHistory.update({
                qpage: b.qpage,
                qsort: b.qsort
            })) {
                loadQuestions(b.qpage, b.qsort)
            }
            if (pHistory.update({
                apage: b.apage,
                afilter: b.afilter
            })) {
                loadActivity(b.apage, b.afilter, true)
            }
            if (pHistory.update({
                reppage: b.reppage,
                repview: b.repview
            })) {
                loadReputation(b.reppage, b.repview)
            }
            if (pHistory.update({
                rpage: b.rpage,
                rfilter: b.rfilter
            })) {
                loadResponses(b.rpage, b.rfilter, true)
            }
            if (pHistory.update({
                anpage: b.anpage,
                ansort: b.ansort
            })) {
                loadAnswers(b.anpage, b.ansort)
            }
            if (pHistory.update({
                fpage: b.fpage,
                fsort: b.fsort
            })) {
                loadFavorites(b.fpage, b.fsort)
            }
        })
    }
    $(document).on("click", "#question-pager a, #tabs-question-user a", function (event) {
        event.preventDefault();
        var u = parsePagedUrl(this.href);
        loadQuestions(u.page, u.sort, this.href);
        pHistory.update({
            qpage: u.page,
            qsort: u.sort
        });
        $.history.load(pHistory.toUrl());
        return false;
    });
    $(document).on("click", "#activity-pager a, #tabs-activity a", function (event) {
        event.preventDefault();
        var c = parsePagedUrl(this.href);
//        loadActivity(c.page, c.filter, $(this).parent().attr("id") == "tabs-activity");
        loadActivity(c.page, c.filter, $(this).parent().attr("id") == "tabs-activity", this.href);
        pHistory.update({
            apage: c.page,
            afilter: c.filter
        });
        $.history.load(pHistory.toUrl());
        return false
    });
    $(document).on("click", "#reputation-pager a, #tabs-reputation a", function (event) {
        event.preventDefault();
        var c = parsePagedUrl(this.href);
        loadReputation(c.page, c.sort, $(this).parent().attr("id") == "tabs-reputation", this.href);
        pHistory.update({
            reppage: c.page,
            repview: c.sort
        });
        $.history.load(pHistory.toUrl());
        return false
    });
    $("#responses-pager a, #tabs-responses a").on("click", function (event) {
        event.preventDefault();
        var c = parsePagedUrl(this.href);
        loadResponses(c.page, c.rfilter, $(this).parent().attr("id") == "tabs-responses");
        pHistory.update({
            rpage: c.page,
            rfilter: c.rfilter
        });
        $.history.load(pHistory.toUrl());
        return false
    });
    $(document).on("click", "#answer-pager a, #tabs-answer-user a", function (event) {
        event.preventDefault();
        var c = parsePagedUrl(this.href);
        loadAnswers(c.page, c.sort);
        pHistory.update({
            anpage: c.page,
            ansort: c.sort
        });
        $.history.load(pHistory.toUrl());
        return false;
    });
    $(document).on("click", "#tags-pager a", function (event) {
        event.preventDefault();
        var c = RegExp(/page=([^&]+).*/).exec(this.href);
        loadTags(this.href);
//        if (c) {
//            loadTags(c[1]);
//        } else {
//            loadTags(1);
//        }
        $.scrollTo($("#tags-title"), 400);
        return false;
    });

    $(document).on("click", "#favorite-pager a, #tabs-favorite-user a", function (event) {
        event.preventDefault();
        var c = parsePagedUrl(this.href);
        loadFavorites(c.page, c.sort, this.href);
        //@todo 临时屏蔽
//        pHistory.update({
//            fpage: c.page,
//            fsort: c.sort
//        });
//        $.history.load(pHistory.toUrl());
        return false;
    })
});