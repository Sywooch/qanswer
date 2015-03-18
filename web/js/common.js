if (typeof(iAsk) == "undefined")
    var iAsk = {};

iAsk.init = (function() {
    var escClick = function() {
        $(document).keyup(function(event) {
            if (event.which == 27) {
                $(".error-notification, .popup, .post-moderator-menu").fadeOutAndRemove();
            }
        })
    };
    return function(initParam){
        iAsk.options = initParam;
        $(function() {
            if (initParam.user.messages) {
                iAsk.notify.showMessages(initParam.user.messages)
            }			
            escClick();
            if (initParam.user.isAnonymous) {
                iAsk.notify.showFirstTime(initParam.site.description)
            }
            iAsk.tags.init();
        });
    }
})();
function appendLoader(a){
    $(a).append('<img class="ajax-loader" src="/images/progress-dots.gif" title="loading..." alt="loading..." />')
};

iAsk.helpers = {};

//window.mem4k = iAsk.helpers;
iAsk.helpers = function() {
    var c = function(e) {
        return e.parent().find("span.edit-field-overlay")
    };
    function a(h, j, k) {
        for (var f = 0; f < k.lenght; f++) {
            var g = k[f];
            try {
                j.css(g, h.css(g))
            } catch(e) {}
        }
    }
    function d(h, f) {
        if (!h.is(":visible")) {
            return
        }
        if (h.val().length != 0) {
            h.css("opacity", 1).css("filter", "").removeClass("edit-field-overlayed");
            return
        } else {
            h.css("opacity", f ? 0.5 : 0.3);
            h.addClass("edit-field-overlayed")
        }
        var e = h.prev(".actual-edit-overlay");
        if (e.length == 0) {
            var g = c(h).text();
            e = h.clone().attr("class", "actual-edit-overlay").attr("name", null).attr("id", null).attr("disabled", "disabled").val(g).css({
                position: "absolute",
                backgroundColor: "white",
                color: "black",
                opacity: 1,
                width: h.width(),
                height: h.height()
            });
            a(h, e, ["font-family", "font-size", "line-height", "text-align"]);
            h.css({
                zIndex: 1,
                position: "relative"
            });
            e.insertBefore(h);
            var n = h.offset().top - e.offset().top;
            if (n != 0) {
                var k = parseInt(e.css("margin-top"));
                var i = k + n;
                if (!h.is("textarea")) {
                    i = parseInt(e.prevAll(":visible").eq(0).css("margin-bottom")) + k
                }
                e.css("margin-top", i)
            }
            var l = h.offset().left - e.offset().left;
            if (l != 0) {
                var j = parseInt(e.css("margin-left"));
                e.css("margin-left", j + l)
            }
        }
    }
    var _mem = {
        bindHelpOverlayEvents: function(e) {
            e.bind("keydown contextmenu",function() {
                iAsk.helpers.hideHelpOverlay($(this))
            }).focus(function() {
                d($(this), true)
            }).blur(function() {
                d($(this))
            }).each(function() {
                d($(this))
            })
        },
        hideHelpOverlay: function(e) {
            e.css("opacity", 1);
            e.css("filter", "");
            e.removeClass("edit-field-overlayed")
        },
        onClickDraftSave: function(e) {
            $(e).click(function() {
                var f = this.href;
                if (!heartbeat.draftSaved()) {
                    heartbeat.ping(function() {
                        window.onbeforeunload = null;
                        window.location.href = f
                    });
                    return false
                }
                window.onbeforeunload = null;
                return true
            });
            return true
        },
        showErrorPopup: function(e, h, f) {
            var $notification = $('<div class="error-notification supernovabg"><h2>' + h + "</h2>" + (f ? "": "(再次单击关闭窗口)") + "</div>");
            var i = function() {
                $notification.fadeOutAndRemove()
            };
            $(e).append($notification);
            $notification.click(i).fadeIn("fast");
            setTimeout(i, (f ? Math.max(2500, h.length * 40) : 1000 * 30))
        },
        addSpinner: function(_container, f) {
            $(_container).append(_mem.getSpinnerImg(f))
        },
        getSpinnerImg: function(e) {
            var f = $('<img class="ajax-loader" src="/images/progress-dots.gif" title="loading..." alt="loading..." />');
            if (e) {
                f.css(e)
            }
            return f
        },
        removeSpinner: function() {
            $("img.ajax-loader").remove()
        },
        bind_hidePopupsOnEscPress: function() {
            $(document).keyup(function(e) {
                if (e.which == 27) {
                    $(".error-notification, .popup, .post-moderator-menu").fadeOutAndRemove();
                    if (genuwine.isVisible()) {
                        genuwine.click()
                    }
                }
            })
        },
        bind_submitOnEnterPress: function(e) {
            e.find("textarea").keyup(function(f) {
                if (f.which == 13 && !f.shiftKey) {
                    e.submit()
                }
            }).keypress(function(f) {
                if (f.which == 13 && !f.shiftKey) {
                    return false
                }
            })
        }
    }
    return _mem;
} ();

iAsk.question = (function() {
    return {
        init : function(d) {
            vote.init();
            comments.init();
//			if (d.canViewVoteCounts) {
//				vote.bindFetchVoteCounts()
//			}
            iAsk.vote_bounty.init(d);
        }
    }
})();
function setSubmitButtonDisabled(form, isDisable) {
    $(form).find("input[type='submit']").attr("disabled", isDisable ? "disabled": "")
}
function showAjaxError(d, e) {
    var $errorNotification = $('<div class="error-notification supernovabg"><h2>' + e + "</h2>(再次单击关闭窗口)</div>");
    var c = function() {
        $(".error-notification").fadeOut("fast",
        function() {
            $(this).remove()
        })
    };
    $errorNotification.click(function(f) {
        c()
    });
    $(d).append($errorNotification);
    $errorNotification.fadeIn("fast");
    setTimeout(c, 1000 * 30);
};

function moveScroller() {
    var width = $("#scroller").width();
    var a = function() {
        var f = $(window).scrollTop();
        var top = $("#scroller-anchor").offset().top;
        var $scroller = $("#scroller");
        if (f > top) {
            $scroller.css({
                position: "fixed",
                top: "0px",
                width: width
            })
        } else {
            if (f <= top) {
                $scroller.css({
                    position: "relative",
                    top: ""
                })
            }
        }
    };
    $(window).scroll(a);
    a()
}
$.fn.extend({
    fadeOutAndRemove: function(a) {
        return this.each(function() {
            var $this = $(this);
            $this.fadeOut("fast", function() {
                $this.remove()
            })
        })
    },
    charCounter: function(param) {
        return this.each(function() {
            $(this).bind("blur focus keyup",function() {
                var _min = param.min;
                var _max = param.max;
                var e = param.setIsValid ||	function() {};
                var _currentLen = this.value ? this.value.length: 0;
                var _classname = _currentLen > _max * 0.8 ? "supernova": _currentLen > _max * 0.6 ? "hot": _currentLen > _max * 0.4 ? "warm": "cool";
                var _tips = "";
                if (_currentLen == 0) {
                    _tips = "至少需要 " + _min + " 个字";
                    e(false);
                } else {
                    if (_currentLen < _min) {
                        _tips = "还需要 " + (_min - _currentLen) + "个字";
                        e(false);
                    } else {
                        _tips = "还可以输入" + (_max - _currentLen) + "个字";
                        e(_currentLen <= _max);
                    }
                }
                var $spanCounter = $(this).parents("form").find("span.text-counter");
                $spanCounter.text(_tips);
                if (!$spanCounter.hasClass(_classname)) {
                    $spanCounter.removeClass("supernova hot warm cool").addClass(_classname)
                }
            })
        })
    },
    addSpinner: function(a) {
        return this.each(function() {
            iAsk.helpers.addSpinner(this, a)
        })
    },
    addSpinnerAfter: function(a) {
        return this.each(function() {
            $(this).after(iAsk.helpers.getSpinnerImg(a))
        })
    },	
    showErrorPopup: function(c, a) {
        return this.each(function() {
            iAsk.helpers.showErrorPopup(this, c, a)
        })
    },
    center: function() {
        this.css("position", "absolute");
        this.css("top", ($(window).height() - this.height()) / 2 + $(window).scrollTop() + "px");
        this.css("left", ($(window).width() - this.width()) / 2 + $(window).scrollLeft() + "px");
        return this;
    },
    helpOverlay: function() {
        iAsk.helpers.bindHelpOverlayEvents(this);
        return this;
    },
    hideHelpOverlay: function() {
        iAsk.helpers.hideHelpOverlay(this);
        return this;
    },
    enable: function() {
        if (arguments.length == 0 || arguments[0]) {
            this.removeAttr("disabled").css("cursor", "pointer");
        } else {
            this.attr("disabled", "disabled").css("cursor", "default");
        }
        return this;
    },
    disable: function() {
        return this.enable(false);
    },	
    loadPopup: function(param) {
        var that = this;
        that.addSpinnerAfter({
            padding: "0 3px"
        });
        $.ajax({
            type: "GET",
            url: param.url,
            dataType: "html",
            success: function(data) {
                var $html = $(data);
                $html.find(".popup-actions-cancel").click(function() {
                    $html.fadeOutAndRemove()
                });
                $html.find("input:radio[disabled=disabled] + label.action-label").addClass("action-disabled");
                if (param.hideDescriptions) {
                    $html.find("ul.action-list > li:not(.action-selected) .action-desc").hide()
                }

                $html.appendTo(that.parent());
                if (param.loaded) {
                    param.loaded($html)
                }
                var g = function() {};
                $html.center().fadeIn("fast", g)
            },
            error: function() {
                that.parent().showErrorPopup("不能载入窗口 - 请重试")
            },
            complete: iAsk.helpers.removeSpinner
        });
        return that;
    },
    delayedHover: function(g, e, h, c) {
        if (this.length == 0) {
            return this
        }
        if (this.length > 1) {
            this.each(function() {
                $(this).delayedHover(g, e, h, c)
            });
            return this
        }
        var f, i;
        h = h || 0;
        c = typeof c == "number" ? c: h;
        function d() {
            if (f) {
                clearTimeout(f)
            }
            if (i) {
                clearTimeout(i)
            }
            f = i = null
        }
        this.hover(function(k) {
            var j = this;
            d();
            f = setTimeout(function() {
                g.call(j, k)
            },
            h)
        },
        function(k) {
            var j = this;
            d();
            i = setTimeout(function() {
                e.call(j, k)
            },
            c)
        });
        return this
    }
});
jQuery.cookie = function(key, value, param) {
    if (typeof value != "undefined") {
        param = param || {};
        if (value === null) {
            value = "";
            param.expires = -1
        }
        var g = "";
        if (param.expires && (typeof param.expires == "number" || param.expires.toUTCString)) {
            var e;
            if (typeof param.expires == "number") {
                e = new Date();
                e.setTime(e.getTime() + (param.expires * 24 * 60 * 60 * 1000))
            } else {
                e = param.expires
            }
            g = "; expires=" + e.toUTCString()
        }
        var l = param.path ? "; path=" + (param.path) : "";
        var f = param.domain ? "; domain=" + (param.domain) : "";
        var n = param.secure ? "; secure": "";
        document.cookie = [key, "=", encodeURIComponent(value), g, l, f, n].join("")
    } else {
        var d = null;
        if (document.cookie && document.cookie != "") {
            var c = document.key.split(";");
            for (var h = 0; h < c.length; h++) {
                var a = jQuery.trim(c[h]);
                if (a.substring(0, key.length + 1) == (key + "=")) {
                    d = decodeURIComponent(a.substring(key.length + 1));
                    break
                }
            }
        }
        return d
    }
};

$.extend({
    URLEncode: function(a) {
        var i = "";
        var k = 0;
        a = a.toString();
        var j = /(^[a-zA-Z0-9_.]*)/;
        while (k < a.length) {
            var g = j.exec(a.substr(k));
            if (g != null && g.length > 1 && g[1] != "") {
                i += g[1];
                k += g[1].length
            } else {
                if (a[k] == " ") {
                    i += "+"
                } else {
                    var e = a.charCodeAt(k);
                    var f = e.toString(16);
                    i += "%" + (f.length < 2 ? "0": "") + f.toUpperCase()
                }
                k++
            }
        }
        return i
    },
    URLDecode: function(e) {
        var c = e;
        var a, f;
        var d = /(%[^%]{2})/;
        while ((m = d.exec(c)) != null && m.length > 1 && m[1] != "") {
            b = parseInt(m[1].substr(1), 16);
            f = String.fromCharCode(b);
            c = c.replace(m[1], f)
        }
        return c
    }
});

function expandPostBody(g, d, a, c) {
    d = d === null ? "": d;
    $(g).addClass("load-prepped").prepend('<a class="load-body expander-arrow-small-hide" style=""></a>&nbsp;').closest("tr").after('<tr class="loaded-body">' + d + '<td class="body-container" ' + (c > 0 ? 'colspan="' + c + '" ': "") + "/></tr>");
    $('tr:has("td.body-container")').children().css("padding", "0px");
    $(".load-body").off("click");
    $(document).on("click", ".load-body", function() {
        var $bodyContainer = $(this).closest("tr").next().show().find(".body-container:first");
        var j;
        if ($bodyContainer.find(".ajax-loader").length > 0) {
            return;
        }
        if ($bodyContainer.is(".body-loaded")) {
            j = $bodyContainer.find("div:first");
            if ($(this).is(".hide-body")) {
                f($bodyContainer, j)
            } else {
                h($bodyContainer, j)
            }
            return;
        }
        $bodyContainer.addSpinner({
            "padding-left": "5px"
        });
        var i = $(this).closest("td").attr("id");
        var appendUrl;
        if (i.indexOf("enable-load-body-") > -1) {
            appendUrl = iAsk.options.links.postView +'/?id=' + i.substr("enable-load-body-".length) + "&op=body"
        } else {
            if (i.indexOf("enable-load-revision-") > -1) {
                appendUrl = iAsk.options.links.revisionsView + '/?id=' + i.substr("enable-load-revision-".length)
            } else {
                appendUrl = $(this).closest("td").data("load-url")
            }
        }
        $.ajax({
            type: "GET",
            url: appendUrl,
            dataType: "html",
            success: function(data) {
                j = $('<div style="display:none">' + data + "</div>");
                $bodyContainer.append(j).trigger("bodyloaded");
                if (a) {
                    a($bodyContainer)
                }
                h($bodyContainer, j)
            },
            error: function(o, p, n) {
                $bodyContainer.showErrorPopup((o.responseText && o.responseText.length < 100 ? o.responseText: "Error occurred when loading post body"))
            },
            complete: iAsk.helpers.removeSpinner
        })
    });
    function h(j, i) {
        j.css("padding", "").height(i.height()).addClass("body-loaded");
        e(j).addClass("hide-body expander-arrow-small-show");
        i.fadeIn("fast")
    }
    function f(j, i) {
        i.fadeOut("fast",
        function() {
            j.height(0).css("padding", "0px");
            e(j).removeClass("hide-body expander-arrow-small-show")
        })
    }
    function e(i) {
        return i.closest("tr").prev().find(".load-body")
    }
};

function removeLoader() {
    $("img.ajax-loader").remove()
};

/*通知*/
iAsk.notify = function() {
    var messageCount = 0;
    var defaultTypeid = -1;
    var messageKey = "m";
    var bindCloseClicks = function(typeId) {
        var $notify = $("#notify-" + typeId);
        var _marginTop = 0;
        if (typeId == defaultTypeid) {
            h()
        } else {
            if (typeId > defaultTypeid) {
                $.post(iAsk.options.links.messageMark, {
                    typeid: typeId
                })
            }
        }
        if (--messageCount > 0) {
            _marginTop = parseInt($("body").css("margin-top").match(/\d+/));
            _marginTop = _marginTop - (_marginTop / (messageCount + 1))
        }
        $notify.fadeOut("fast",	function() {
            $("body").animate({marginTop: _marginTop + "px"},"fast", "linear");
            $notify.remove();
        })
    };
    var createNotifyNode = function(param) {
        var html = "<div" + (param.messageTypeId ? ' id="notify-' + param.messageTypeId + '"': "") + ' style="display:none"><span class="notify-close"><a title="忽略">&times;</a></span><span class="notify-text">' + param.text + "</span>";
        if (param.showProfile) {
            var o = encodeURIComponent(iAsk.options.links.userView + '/?id=' + param.userId + "&tab=activity");
            html += ' 查看 <a href="'+iAsk.options.links.messageMark+'?typeid=' + param.messageTypeId + "&returnurl=" + escape(o) + '">账户</a>.'
        }
        html += "</div>";
        var $html = $(html);
        $html.find(".notify-close").click(function() {
            bindCloseClicks(param.messageTypeId)
        });
        $("#notify-container").append($html)
    };
    var h = function(v) {
        $.cookie(messageKey, (v ? v: "0"), {
            expires: 90,
            path: "/"
        })
    };
    var j = function() {
        var k = parseInt($.cookie(messageKey));
        if (isNaN(k)) {
            k = 0
        }
        if (k < 5) {
            $(".module.newuser").show();
            h(++k)
        }
    };
    var closeNotify = function() {
        $("#notify-container div").fadeIn("slow")
    };
    var f = function() {
        $("body").animate({marginTop: "2.5em"},"fast","linear")
    };
    return {
        showFirstTime: function(k) {
            if ($.cookie(messageKey)) {
                j()
            } else {
                $(".module.newuser").show();
                if (!/\/users\/(login|authenticate)/i.test(window.location)) {
                    f();
                    createNotifyNode({
                        messageTypeId: defaultTypeid,
                        text: "Welcome to " + k + ' &mdash; check out the <a onclick="StackExchange.notify.closeFirstTime(); return false;" href="/faq">FAQ</a>!'
                    });
                    closeNotify()
                }
            }
        },
        showMessages: function(messages) {
            messageCount = messages.length;
            for (var i = 0; i < messageCount; i++) {
                createNotifyNode(messages[i]);
            }
            closeNotify();
        },
        show: function(message, typeId) {
            f();
            createNotifyNode({
                text: message,
                messageTypeId: typeId
            });
            closeNotify();
        },
        close: bindCloseClicks,
        closeFirstTime: function() {
            h();
            document.location = "/faq"
        },
        getMessageText: function(k) {
            return $("#notify-" + k + " .notify-text").text()
        }
    }
} ();

/*tags*/
iAsk.tags = (function() {
    var e = function(k) {
        var l = "";
        var n = "";
        k.each(function() {
            var o = false;
            n = $(this).text();
            if (n.indexOf("#") > -1) {
                n = n.replace(/#/g, "ñ")
            }
            if (n.indexOf("+") > -1) {
                n = n.replace(/\+/g, "ç")
            }
            if (n.indexOf(".") > -1) {
                n = n.replace(/\./g, "û")
            }
            if (n.indexOf("*") > -1) {
                o = true
            }
            if (o) {
                l += "div.tags:regex(class, t-" + n.replace(/\*/g, ".*") + "),"
            } else {
                l += "div.t-" + n + ","
            }
        });
        if (l.length > 0) {
            l = l.substring(0, l.length - 1)
        }
        return l
    };
    function submitTag(cmdNum, tagname) {
        var userId = "";
        if (typeof forUserId != "undefined") {
            userId = forUserId
        }
        $.post($("#user-save-preference").attr('href'), {
            fkey: iAsk.options.user.fkey,
            key: cmdNum,
            value: tagname,
            forUserId: userId
        });
    }
    var bindTagClick = function(inputTag, divTag, count, posttag, inFocus, isSubmit) {
        var tags = $(inputTag).val();
        var tagList = sanitizeAndSplitTags(tags, true);
        var isExist = false;
        for (var i = 0; i < tagList.length; i++) {
            if ($.trim(tagList[i]).length != 0) {
                $(divTag).children().each(function() {
                    if ($(this).text() == tagList[i]) {
                        $(this).fadeTo(500, 0.1).fadeTo(500, 1);
                        isExist = true;
                        return;
                    }
                });
                if (!isExist) {
                    var n = $.URLEncode(tagList[i]);
                    $(divTag).append('<a id="' + tagList[i] + '" href="/questions/tagged/' + n + '" class="' + posttag + '" title="show questions tagged \'' + tagList[i] + "'\">" + tagList[i] + "</a> ");
                    processTags(count);
                }
                isExist = false;
            }
        }
        $(inputTag).val("");
        if (inFocus) {
            $(inputTag).focus()
        }
        if (!isSubmit) {
            submitTag(count, $(divTag).text())
        }
        applyPrefs();
    };
    var processTags = function(num) {
        var spanTag = "<span class=\"delete-tag\" onmouseover=\"$(this).attr('class', 'delete-tag-hover')\" onmouseout=\"$(this).attr('class', 'delete-tag')\" title=\"删除\"></span>";
        if (num == 0) {
            $("#ignoredTags > .post-tag").after(spanTag)
        }
        if (num == 25) {
            $("#ignoredTags > .post-tag:last").after(spanTag)
        }
        $("#ignoredTags > .delete-tag").click(function() {
            $(this).prev().remove();
            $(this).remove();
            submitTag(25, $("#ignoredTags").text());
            applyPrefs()
        });
        if (num == 0) {
            $("#interestingTags > .post-tag").after(spanTag)
        }
        if (num == 20) {
            $("#interestingTags > .post-tag:last").after(spanTag)
        }
        $("#interestingTags > .delete-tag").click(function() {
            $(this).prev().remove();
            $(this).remove();
            submitTag(20, $("#interestingTags").text());
            applyPrefs();
        })
    };
    var applyPrefs = function(r, k) {
        var aIgnoredTags = $("#ignoredTags > a");
        var aInterestingTags = $("#interestingTags > a");
        var aInferredTags = $("#inferredTags > a");
        if (r && aIgnoredTags.length == 0 && aInterestingTags.length == 0 && aInferredTags.length == 0) {
            return;
        }
        $("div.question-summary").removeClass("tagged-ignored tagged-ignored-hidden tagged-interesting");
        var o = e(aIgnoredTags);
        var q = e(aInterestingTags);
        var p = e(aInferredTags);
        if (k) {
            for (var l = 0; l < k.length; l++) {
                var w = k[l];
                if (w.indexOf("#") > -1) {
                    w = w.replace(/#/g, "ñ")
                }
                if (w.indexOf("+") > -1) {
                    w = w.replace(/\+/g, "ç")
                }
                if (w.indexOf(".") > -1) {
                    w = w.replace(/\./g, "û")
                }
                var v = new RegExp("div.t-" + w + "(,|$)", "g");
                o = o.replace(v, "");
                q = q.replace(v, "");
                p = p.replace(v, "")
            }
        }
        if (o.length > 0) {
            var taggedIgnored = $("#hideIgnored").is(":checked") ? "tagged-ignored-hidden": "tagged-ignored";
            $(o).closest("div.question-summary").addClass(taggedIgnored);
        }
        if (q.length > 0) {
            $(q).closest("div.question-summary").addClass("tagged-interesting");
        }
        if (p.length > 0) {
            $(p).closest("div.question-summary").addClass("tagged-interesting");
        }
    };
    var d = function(l, o) {
        var $tagMenu = $("#tag-menu");
        if ($("#interestingTags").length > 0) {
            if (o) {
                var $interestingTags = $("#interestingTags a").filter(function() {
                    return $(this).text() == l;
                });
                $interestingTags.add($interestingTags.next()).remove();
                applyPrefs();
            } else {
                $("#interestingTag").val(l);
                bindTagClick("#interestingTag", "#interestingTags", 20, "post-tag", false, true);
            }
        }
        iAsk.helpers.addSpinner($tagMenu);
        $.ajax({
            type: "POST",
            url: "/tags/" + encodeURIComponent(l) + "/favorite",
            data: {
                unfavorite: o,
                fkey: iAsk.options.user.fkey
            },
            dataType: "html",
            success: function(p) {
                c(k.html($("" + p)), l)
            },
            error: function(q, r, p) {},
            complete: iAsk.helpers.removeSpinner
        })
    };
    var j = function(l, n) {
        iAsk.helpers.addSpinner($("#tag-menu"));
        $.ajax({
            type: "POST",
            url: "/tags/" + encodeURIComponent(l) + "/subscription",
            data: {
                unsubscribe: n,
                fkey: iAsk.options.user.fkey
            },
            dataType: "html",
            success: function(o) {
                c($("#tag-menu").html($("" + o)), l)
            },
            error: function(p, q, o) {},
            complete: iAsk.helpers.removeSpinner
        });
    };
    var c = function(k, l) {
        k.find(".tm-se-subscription").click(function() {
            j(l, $(this).hasClass("tm-se-unsubscribe"))
        }).end().find(".tm-favorite-on, .tm-favorite-off").click(function() {
            d(l, $(this).hasClass("tm-favorite-on"))
        })
    };
    var g = function() {
        if (g.initialized) {
            return
        }
        g.initialized = true;
        if (iAsk.options.isMobile) {
            return
        }
        var n;
        var k = false;
        $("document").on("mouseenter", ".post-tag", function() {
            if ($(this).attr("href").charAt(0) != "/") {
                return false
            }
            var o = $(this).attr("title", "");
            var p = o.text();
            if (p.indexOf("*") > -1) {
                return
            }
            n = setTimeout(function() {
                $("#tag-menu").remove();
                var q = $("<div id='tag-menu'/>").hide().css({
                    position: "absolute",
                    left: o.offset().left + "px",
                    top: (o.offset().top + o.outerHeight() + 3) + "px"
                }).appendTo("body").hover(function() {
                    k = true;
                },
                function() {
                    k = false;
                    $("#tag-menu").remove()
                });
                iAsk.helpers.addSpinner(q);
                $.ajax({
                    type: "GET",
                    url: iAsk.options.links.subscriber+"?name=" + encodeURIComponent(p),
                    dataType: "html",
                    success: function(r) {
                        c($("" + r).prependTo(q), p)
                    },
                    error: function(s, t, r) {},
                    complete: iAsk.helpers.removeSpinner
                });
                q.show();
            },500);
            return false;
        });
        $(document).on("mouseleave",".post-tag",function() {
            clearTimeout(n);
            setTimeout(function() {
                if (!k) {
                    $("#tag-menu").remove()
                }
            },
            100)
        });
    };
    return {
        applyPrefs: applyPrefs,
        init: function() {
            g();
            if ($("#interestingTags").length == 0) {
                return;
            }
            processTags(0);
            $("#ignoredAdd").click(function() {
                bindTagClick("#ignoredTag", "#ignoredTags", 25, "post-tag", true)
            });
            $("#interestingAdd").click(function() {
                bindTagClick("#interestingTag", "#interestingTags", 20, "post-tag", true)
            });
            $("#hideIgnored").click(function() {
                submitTag(30, $(this).is(":checked"));
                applyPrefs();
            });
            bindTagFilterAutoComplete("#ignoredTag");
            bindTagFilterAutoComplete("#interestingTag");
        }
    }
})();
/**
 * @param j
 * @param a
 * @return
 */
function sanitizeAndSplitTags(tags, a) {
    if (!sanitizeAndSplitTags.noDiac) {
        var c = {
            "àåáâäãåą": "a",
            "èéêëę": "e",
            "ìíîïı": "i",
            "òóôõöøő": "o",
            "ùúûü": "u",
            "çćč": "c",
            "żźž": "z",
            "śşš": "s",
            "ñń": "n",
            "ýŸ": "y",
            "ł": "l",
            "đ": "d",
            "ß": "ss",
            "ğ": "g",
            "Þ": "th"
        };
        sanitizeAndSplitTags.noDiac = function(k) {
            for (var i in c) {
                k = k.replace(new RegExp("[" + i + "]", "g"), c[i])
            }
            return k
        }
    }
    tags = $.trim(tags).replace(/([A-Za-z0-9])\+(?=[A-Za-z0-9])/g, "$1 ");	//?=含义？
    var tagsArray = tags.split(/[\s|,;]+/);

    var f = [];
    for (var i = 0; i < tagsArray.length; i++) {
        var h = sanitizeAndSplitTags.noDiac(tagsArray[i].toLowerCase()).replace(/_/g, "-");

        var e = "[^\u4e00-\u9fa5a-z0-9.#+" + (a ? "*": "") + "-]";
        h = h.replace(new RegExp(e, "g"), "");
        h = h.replace(/^[#+-]+/, "");
        h = h.replace(/[.-]+$/, "");
        if (h.length > 0 && $.inArray(h, f) == -1) {
            f.push(h)
        }
    }
    return f
}

/*end tags*/

var vote = function() {
    var voteTypeIds = {
        informModerator : 	-1,
        undoMod : 			0,
        acceptedByOwner : 	1,
        upMod : 			2,
        downMod : 			3,
        offensive : 		4,
        favorite : 			5,
        close : 			6,
        reopen : 			7,
        bountyClose : 		9,
        deletion : 			10,
        undeletion : 		11,
        spam : 				12
    };
    var hasOpenBounty;
    var fetchVoteTitle = "View upvote and downvote totals";
    var bindAnonymousDisclaimers = function() {
        var anchor = '<a href="'+ iAsk.options.links.login + '"?returnurl=' + escape(document.location) + '">登录或注册</a>';
        unbindVoteClicks().click(function() {
            showNotification($(this), "请首先 " + anchor + " 才能继续投票.")
        });
//		getFlagClick().unbind("click").click(function() {
//			showNotification($(this), "请首先 " + anchor + " 才能举报.")
//		})
    };
    var promptToLogin = false;
    var showPromptToLogin = function(jClicked) {
        if (promptToLogin) {
            var verb = jClicked.is('[id^="flag-post-"]') ? "flag" : jClicked
                    .is(".star-off") ? "favorite" : "vote for";
//			showNotification(jClicked,'Please <a href="/users/login?returnurl='
//							+ escape(document.location)
//							+ '">登录或注册</a> to ' + verb
//							+ " this post.")
        }
        return promptToLogin
    };

    var getFlagClick = function() {
        return $("div.post-menu a[id^='flag-post-']")
    };	
    var getProtectClick = function() {
        return $("div.post-menu a[id^='protect-post-']");
    };
    var getUnprotectClick = function() {
        return $("div.post-menu a[id^='unprotect-post-']")
    };
    var getLockClick = function() {
        return $("div.post-menu a[id^='lock-post-']")
    };
    var getUnlockClick = function() {
        return $("div.post-menu a[id^='unlock-post-']")
    };

    var isUpSelected = function(jUp) {
        return jUp.hasClass("vote-up-on")
    };
    var isDownSelected = function(jDown) {
        return jDown.hasClass("vote-down-on")
    };
    var bindVoteClicks = function(jDivVote) {
        if (!jDivVote) {
            jDivVote = "div.vote";
        }
        $(jDivVote).find(".vote-up-off").unbind("click").click(function() {
            vote.up($(this));
        });
        $(jDivVote).find(".vote-down-off").unbind("click").click(function() {
            vote.down($(this));
        })
    };
    var unbindVoteClicks = function(jClicked) {
        var jDiv = jClicked ? jClicked.closest("div.vote") : $("div.vote");
        return jDiv.find(".vote-up-off, .vote-down-off").unbind("click")
    };
    var fetchVotesCast = function(questionId) {
        $.ajax( {
            type : "GET",
            url : "/post/" + questionId + "/vote",
            dataType : "json",
            success : vote.highlightExistingVotes
        })
    };
    var isFavoriteSelected = function(jFavorite) {
        return jFavorite.hasClass("star-on")
    };

    var getAccepted = function() {
        return $("div.vote a[id^='vote-accepted-']");
    };	
    var e = function() {
        return $("div.post-menu a[id^='lock-post-']")
    };	
    var getPostId = function(jClicked) {
        return jClicked.closest("div.vote").find("input").val()
    };
    var reset = function(jUp, jDown) {
        jUp.removeClass("vote-up-on");
        jDown.removeClass("vote-down-on")
    };
    var updateModScore = function(jClicked, incrementAmount) {
        var jScore = jClicked.siblings("span.vote-count-post");
        var currentScore;
        if (jScore.find(".vote-count-separator").length > 0) {
            var up = parseInt(jScore.children(":first").text(), 10);
            var down = Math.abs(parseInt(jScore.children(":last").text(), 10));
            currentScore = up - down;
            jScore.css("cursor", "pointer").unbind("click").attr("title",fetchVoteTitle).click(function() {
                fetchVoteCounts($(this))
            })
        } else {
            currentScore = parseInt(jScore.text(), 10)
        }
        jScore.text(currentScore + incrementAmount)
    };
    var submitModVote = function(jClicked, voteTypeId) {
        unbindVoteClicks(jClicked);
        var postId = getPostId(jClicked);
        submit(jClicked, postId, voteTypeId, modVoteResult)
    };
    var submit = function(jClicked, postId, voteTypeId, callback, optionalFormData, completeCallback) {
        var formData = {
            fkey : iAsk.options.user.fkey
        };
        if (optionalFormData) {
            for ( var name in optionalFormData) {
                formData[name] = optionalFormData[name]
            }
        }
        alert(jClicked.attr('href'));
        $.ajax( {
            type : "POST",
            url : jClicked.attr('href'),
            data : formData,
            dataType : "json",
            success : function(data) {
                callback(jClicked, postId, data)
            },
            error : function() {
                showNotification(jClicked, "发生错误 - 请重试.")
            },
            complete : completeCallback
        })
    };
    var modVoteResult = function(jClicked, postId, data) {
        if (data.Success) {
            if (data.Message) {
                showFadingNotification(jClicked, data.Message)
            }
//			if (data.ShowShareTip) {
//				iAsk.helpers.question.showShareTip()
//			}
        } else {
            if (window.console && window.console.firebug && (!data.Message || data.Message.length < 5)) {
                showNotification(jClicked,"FireBug seems to be enabled, which can sometimes interfere with voting;<br>please refresh the page to see if your vote was processed.<br><br>If this persists, consider disabling FireBug for this site.")
            } else {
                showNotification(jClicked, data.Message);
                reset(jClicked, jClicked);
                jClicked.parent().find("span.vote-count-post").text(data.NewScore);
                if (data.LastVoteTypeId) {
                    selectPreviousVote(jClicked, data.LastVoteTypeId)
                }
            }
        }
        bindVoteClicks(jClicked.parent())
    };
    var selectPreviousVote = function(jClicked, voteTypeId) {
        var span, spanSelectedClass;
        if (voteTypeId == voteTypeIds.upMod) {
            span = ".vote-up-off";
            spanSelectedClass = "vote-up-on"
        } else {
            if (voteTypeId == voteTypeIds.downMod) {
                span = ".vote-down-off";
                spanSelectedClass = "vote-down-on"
            }
        }
        if (span) {
            jClicked.closest("div.vote").find(span).addClass(spanSelectedClass)
        }
    };	
    var showNotification = function(jClicked, msg) {
        iAsk.helpers.showErrorPopup(jClicked.parent(), msg)
    };

    /* flag */
    var bindFlagClicks = function(jClicks) {
        var postid = jClicks.attr("id").substring("flag-post-".length);

        jClicks.loadPopup({
            url: iAsk.options.links.popup+"?do=flag&postid="+postid,
            loaded: loadFunc,
            hideDescriptions: true,
            actionSelected: C,
        })
    };
    var loadFunc = function(popupHtml) {
        P = null;
        popupHtml.find("form").submit(function() {
            submitFlag(popupHtml);
            return false;
        });
        $("#flag-load-close").unbind("click").click(function() {
            submitFlagLoad(popupHtml, $(this))
        })
    };
    var C = function(am) {
        var an = am.find("input:radio").attr("id").substr("flag-".length);
        var al = an == voteTypeIds.offensive || an == voteTypeIds.spam;
        $(".flag-remaining-spam").toggle(al);
        $(".flag-remaining-inform").toggle(!al)
    };	
    var submitFlagLoad = function(popupHtml, ao) {
        ao.closest("li").siblings("li").trigger("hide-action");
        if (P) {
            popupHtml.fadeOut("fast");
            P.fadeIn("fast")
        } else {
            ao.siblings(".action-name").addSpinner({
                margin: "0 5px"
            });
            var postid = popupHtml.attr("id").substr("flag-popup-".length);
            var _jClick = $("#flag-post-" + postid);
            var isClosePopupForFlagging = $("#flag-isClosePopupForFlagging").val() == "true";
            submitForFlagging(_jClick, postid, isClosePopupForFlagging, popupHtml)
        }
        ao.removeAttr("checked")
    };
    var submitFlag = function(popupHtml) {
        var postid = popupHtml.attr("id").substr("flag-popup-".length);
        var jClick = $("#flag-post-" + postid);
        var typeid = popupHtml.find('input[name="flag-post"]:checked').val();
        if (typeid == voteTypeIds.informModerator) {
            submitInformMod(popupHtml, postid, jClick)
        } else {
            submit(jClick, postid, typeid, flagCallback, null,function() {
                popupHtml.fadeOutAndRemove()
            })
        }
    };
    var submitInformMod = function(popupHtml, postid, jClick) {
        var modAttention = popupHtml.find(".mod-attention-subform");
        var $radio = modAttention.find("input:radio:checked");
        var message = $radio.val() == "other" ? modAttention.find('textarea[name="flag-reason"]').val() : $radio.val();
        $.ajax({
            type: "POST",
            url: iAsk.options.links.messageInfoMod + "?postid="+postid,
            dataType: "json",
            data: {
                fkey: iAsk.options.user.fkey,
                msg: message
            },
            success: function(data) {
                jClick.parent().showErrorPopup(data.Message)
            },
            error: function(ar, au, at) {
                jClick.parent().showErrorPopup(ar.responseText && ar.responseText.length < 100 ? ar.responseText: "An error occurred during submission")
            },
            complete: function() {
                popupHtml.fadeOutAndRemove()
            }
        })
    };	
    var flagCallback = function(am, an, al) {
        if (al.Refresh) {
            location.reload(true)
        } else {
            am.parent().showErrorPopup(al.Message)
        }
    };

    var submitForFlagging = function(_jClick, postid, isClosePopupForFlagging, popupHtml) {
        var al = _jClick.parent();
        if (!isClosePopupForFlagging) {
            _jClick.addSpinnerAfter({
                padding: "0 3px"
            })
        }
        $.ajax({
            type: "GET",
            url: iAsk.options.links.popup+"?do=close&postid="+postid+"&isForFlagging=1",
            dataType: "html",
            success: function(data) {
                var $data = $(data);
                if (popupHtml) {
                    popupHtml.fadeOut("fast");
                    P = $data
                }
                $data.appendTo(al);
                popupProcess($data, _jClick, popupHtml);
                $data.center().fadeIn("fast");
            },
            error: function() {
                al.showErrorPopup("出错了 - 请重试")
            },
            complete: iAsk.helpers.removeSpinner
        })
    };
    var popupProcess = function($flaggingHtml, jClick, popupHtml) {
        var $actionsCancel = $flaggingHtml.find(".popup-actions-cancel");
        if (popupHtml) {
            $actionsCancel.text("back").click(function() {
                $flaggingHtml.fadeOut("fast");
                popupHtml.fadeIn("fast")
            })
        } else {
            $actionsCancel.click(function() {
                $flaggingHtml.fadeOutAndRemove()
            })
        }
        var $popupSubmit = $flaggingHtml.find(".popup-submit");
        $flaggingHtml.find('input[type=radio]:not(input[name="existing-close"])').click(function() {
            var $this = $(this);
            var $li = $this.closest("li");
            if ($li.hasClass("action-selected")) {
                return;
            }
            if (!showPane($this)) {
                $popupSubmit.enable();
                $flaggingHtml.find(".popup-active-pane li.action-selected").removeClass("action-selected");
                $li.addClass("action-selected")
            }
        });
    };	
    var showPane = function(_$input) {
        var paneid = _$input.attr("id").substr("close-".length);
        var $pane = $("#pane" + paneid);
        if ($pane.length == 0) {
            return false
        }
        $("#pane-main").removeClass("popup-active-pane").hide();
        $pane.addClass("popup-active-pane").show();
        _$input.closest("div.popup").find(".popup-actions-cancel").html("返回").unbind("click").click(bindBackEvent);
        window["pane" + paneid]();
        return true;
    };
    var bindBackEvent = function() {
        $(".popup-subpane").removeClass("popup-active-pane").hide();
        $("#pane-main").addClass("popup-active-pane").show().find("input[type=radio]:checked").removeAttr("checked").end().find("li.action-selected").removeClass("action-selected");
    };

    /* endflag */

    var favorite_init = function() {
        $(document).on("click",".star-off:not(.disabled)", function(event) {
            favorite($(this));
            event.preventDefault();
        })
    };

    var favorite = function(jClicked) {
        if (showPromptToLogin(jClicked)) {
            return;
        }
        jClicked.addClass("disabled");
        var jFavoriteCount = jClicked.parent().find("div.favoritecount b");
        var count = parseInt("0" + jFavoriteCount.text().replace(/^\s+|\s+$/g, ""), 10);
        if (isFavoriteSelected(jClicked)) {
            jClicked.removeClass("star-on");
            jFavoriteCount.removeClass("favoritecount-selected").text((count-- <= 0) ? "" : count)
        } else {
            jClicked.addClass("star-on");
            jFavoriteCount.addClass("favoritecount-selected").text(++count)
        }
        var postId = getPostId(jClicked) || jClicked.siblings("input[type=hidden]").val();
        submit(jClicked, postId, voteTypeIds.favorite, function(data) {
            jClicked.removeClass("disabled")
        })
    };

    /**delete**/
    var bindDeleteClicks = function(callback) {
        $("div.post-menu *[id^='delete-post-']").unbind("click").click(function() {
            submitDeletion($(this), callback);
            return false;
        })
    };
    var submitDeletion = function(jClick, callback) {
        var postId = jClick.attr("id").substring("delete-post-".length);
        var isUndelete = jClick.text().indexOf("恢复") > -1;
        if (confirm("投票" + (isUndelete ? "恢复": "删除") + "该帖子?")) {
            submit(jClick, postId, (isUndelete ? voteTypeIds.undeletion: voteTypeIds.deletion), callback || deletion)
        }
    };
    var deletion = function(jClicked, postid, data) {
        var isUndelete = jClicked.text().indexOf("恢复") > -1;
        if (data && data.Success) {
            if (data.Message) {
                jClicked.text(data.Message);
                if (data.NewScore < 0) {
                    var isDelQuestion = $("#question:has(a[id='delete-post-" + postid + "'])").length > 0;
                    var postContainer = isDelQuestion ? "#question, div.answer": "#answer-" + postid;
                    deletionResult($(postContainer), !isUndelete)
                } else {
                    showNotification(jClicked, "该帖子还需要其他用户的 " + data.NewScore + " 次投票才能" + (isUndelete ? "恢复删除": "删除"));
                }
            } else {
                location.reload(true);
            }
        } else {
            var msg = (data && data.Message) ? data.Message: "出错了";
            iAsk.helpers.showErrorPopup(jClicked.parent(), msg)
        }
    };
    var deletionResult = function(container, isDelete) {
        if (isDelete) {
            $("div.question-status:has(span:contains('delete'))").show();
            container.addClass("deleted-answer").find("a[id^='delete-post-']").addClass("deleted-post").end()
                    .find("div[id^='comments-']").addClass("comments-container-deleted").end()
                    .find("a[id^='comments-link-']").addClass("comments-link-deleted");
        } else {
            document.location.reload(true);
        }
    };	
    /**enddelete**/

    /*accept*/
    var submitAccepted = function(jClick) {
        var postid = jClick.attr("id").substring("vote-accepted-".length);
        getAccepted().unbind("click");
        submit(jClick, postid, voteTypeIds.acceptedByOwner, function(_jclick, _postid, data) {
            if (data.Success) {
                $(".vote-accepted-off").removeClass("vote-accepted-on");
                var P = parseInt(data.Message, 10);
                if (P == voteTypeIds.acceptedByOwner) {
                    _jclick.addClass("vote-accepted-on")
                } else {
                    _jclick.removeClass("vote-accepted-on")
                }
            } else {
                showNotification(_jclick, data.Message)
            }
            getAccepted().click(function() {
                submitAccepted($(this));
            })
        })
    };

    return {
        init : function() {
            promptToLogin = !(iAsk.options.user.isRegistered);
//			hasOpenBounty = userHasOpenBounty;
            if (promptToLogin) {
                bindAnonymousDisclaimers();
            } else {
                bindVoteClicks();
//				getFlagClick().unbind("click").click(function() {
//					bindFlagClicks($(this))
//				});	
                getProtectClick().unbind("click").click(function() {
                    var postid = this.id.substring("protect-post-".length);
                    if (confirm("是否确定要让本问题回答受限？")) {
                        $.ajax({
                            type: "POST",
                            url: this.href,
                            data: {
                                id: postid,
                                fkey: iAsk.options.user.fkey
                            },
                            success: function(ap) {
                                location.reload(true)
                            }
                        })
                    }
                    return false
                });				
                getLockClick().unbind("click").click(function() {
                    var postid = this.id.substring("lock-post-".length);
                    if (confirm("是否确定要锁定本问题？")) {
                        $.ajax({
                            type: "post",
                            url: this.href,
                            data: {
                                id: postid,
                                fkey: iAsk.options.user.fkey
                            },
                            success: function(ap) {
                                location.reload(true);
                            }
                        })		
                    }
                    return false
                });				
                getUnlockClick().unbind("click").click(function() {
                    var postid = this.id.substring("unlock-post-".length);
                    if (confirm("是否确定解锁本问题？")) {
                        $.ajax({
                            type: "post",
                            url: this.href,
                            data: {
                                id: postid,
                                fkey: iAsk.options.user.fkey
                            },
                            success: function(ap) {
                                location.reload(true);
                            }
                        });
                    }
                    return false
                });				
            }
            vote.favorite_init();
            bindDeleteClicks();
            getAccepted().unbind("click").click(function() {
                submitAccepted($(this));
            });

            getUnprotectClick().unbind("click").click(function() {
                var postid = this.id.substring("unprotect-post-".length);
                if (confirm("是否取消本问题的问答受限？")) {
                    $.ajax({
                        type: "POST",
                        url: this.href,
                        data: {
                            id: postid,
                            fkey: iAsk.options.user.fkey
                        },
                        success: function(ap) {
                            location.reload(true)
                        }
                    })
                }
                return false
            })
        },
        up : function(jClicked) {
            var jUp = jClicked.parent().find(".vote-up-off");
            var jDown = jClicked.parent().find(".vote-down-off");
            var isSelected = isUpSelected(jUp);
            var isReversal = isDownSelected(jDown);
            var incrementAmount = isSelected ? -1 : (isReversal ? 2 : 1);
            updateModScore(jClicked, incrementAmount);
            reset(jUp, jDown);
            if (!isSelected) {
                jUp.addClass("vote-up-on")
            }
            submitModVote(jClicked, isSelected ? voteTypeIds.undoMod : voteTypeIds.upMod)
        },
        down : function(jClicked) {
            var jUp = jClicked.parent().find(".vote-up-off");
            var jDown = jClicked.parent().find(".vote-down-off");
            var isSelected = isDownSelected(jDown);
            var isReversal = isUpSelected(jUp);
            var incrementAmount = isSelected ? 1 : (isReversal ? -2 : -1);
            updateModScore(jClicked, incrementAmount);
            reset(jUp, jDown);
            if (!isSelected) {
                jDown.addClass("vote-down-on")
            }
            submitModVote(jClicked, isSelected ? voteTypeIds.undoMod: voteTypeIds.downMod)
        },
        favorite_init : favorite_init,
        highlightExistingVotes : function(jsonArray) {
            $.each(jsonArray,function() {
                var jDiv = $("div.vote:has(input[value="+ this.PostId + "])");
                switch (this.VoteTypeId) {
                    case voteTypeIds.upMod:
                        jDiv.find(".vote-up-off").addClass(	"vote-up-on");
                        break;
                    case voteTypeIds.downMod:
                        jDiv.find(".vote-down-off").addClass("vote-down-on");
                        break;
                    case voteTypeIds.favorite:
                        jDiv.find(".star-off").addClass("star-on");
                        jDiv.find("div.favoritecount b").addClass("favoritecount-selected");
                        break;
                    default:
                        //mem4k.debug.log("site.vote.js > highlightExistingVotes has no case for "
                            //			+ this.VoteTypeId);
                        break
                }
            });
            votesCast = null
        },
        bindFetchVoteCounts : function() {
            $(".vote-count-post").attr("title", fetchVoteTitle).css("cursor","pointer").unbind("click").click(function() {
                fetchVoteCounts($(this))
            })
        },
        submit:submit,
        voteTypeIds:voteTypeIds,
        getPostId:getPostId
    }
}();

iAsk.vote_bounty = (function() {
    var hasOpenBounty;
    var bountyClose_callback = function(jClicked, postId, data) {
        var jButtons = $(".bounty-vote");
        if (data.Success) {
            $("#bounty-notification").remove();
            hasOpenBounty = false;
            var jRep = jClicked.closest("td.votecell").parent().find("span.reputation-score:last");
            if (data.Message) {
                var jData = $(data.Message);
                jRep.text(jData.text()).attr("title", jData.attr("title"));
            }
            var jContainers = jClicked.closest("div.vote").find(".bounty-award-container");
            if (jContainers.length > 1) {
                var jAward = jContainers.filter(":first").find(".bounty-award");
                var previousAward = parseInt(jAward.text(), 10);
                var currentAward = parseInt(jClicked.text(), 10);
                jAward.text("+" + (previousAward + currentAward));
                jButtons.remove();
            } else {
                jClicked.unbind("mouseenter mouseleave").removeClass("bounty-vote bounty-vote-off");
                jButtons.not(jClicked).remove();
            }
        } else {
            iAsk.helpers.showErrorPopup(jClicked.parent(), data.Message);
            jButtons.removeClass("disabled");
        }
    };
    var confirmBountyAward = function(postId) {
        if (hasOpenBounty) {
            return confirm("确实要将悬赏授予该回答? 一旦设置不可修改!");
        }
        return true;
    };
    var bountyClose = function(jClicked) {
        var postId = vote.getPostId(jClicked);
        if (!confirmBountyAward(postId)) {
            return;
        }
        $(".bounty-vote").addClass("disabled");
        vote.submit(jClicked, postId, vote.voteTypeIds.bountyClose, bountyClose_callback);
    };
    var bountyClose_init = function() {
        $(".bounty-vote").hover(function() {
            $(this).removeClass("bounty-vote-off");
        },
        function() {
            $(this).addClass("bounty-vote-off");
        });
//		$(".bounty-vote:not(.disabled)").live("click",function() {
        $(document).on("click", ".bounty-vote:not(.disabled)",function() {
            bountyClose($(this));
            return false;
        })
    };
    var bounty_init = function() {
        var link = $("#bounty-link");
        link.click(function() {
            $("#bounty").toggle();
            link.text(link.text().indexOf("发布") > -1 ? "隐藏": "发布悬赏")
        });
        var button = $("#bounty-start");
        button.click(function() {
            var amount = $("#bounty-amount").val();
            if (!confirm("确实要对该问题悬赏"+ amount + "威望?")) {
                return
            }
            button.disable();
            bounty_start(amount)
        })
    };
    var bounty_start = function(amount) {
        var questionId = $("#question div.vote input:first").val();
        var div = $("#bounty");
        var button = $("#bounty-start");
        button.addSpinnerAfter({
            "padding-left": "3px"
        });
        $.ajax({
            type: "POST",
            url: iAsk.options.links.bountyStart+"?qid="+questionId,
            dataType: "json",
            data: {
                fkey: iAsk.options.user.fkey,
                amount: amount
            },
            success: function(data) {
                if (data.Success) {
                    location.reload(true);
                } else {
                    div.showErrorPopup(data.Message);
                    button.enable();
                }
            },
            error: function(res, textStatus, errorThrown) {
                div.showErrorPopup(res.responseText && res.responseText.length < 100 ? res.responseText: "发生了一个错误")
            },
            complete: iAsk.helpers.removeSpinner
        })
    };
    return {
        init: function(initParams) {
            hasOpenBounty = !!initParams.hasOpenBounty;
            if (initParams.canOpenBounty) {	//能够发布新悬赏
                bounty_init();
            }
            if (initParams.hasOpenBounty) {	//当前有悬赏
                bountyClose_init();
            }
        }
    }
})();


var comments = function() {
    var e;
    var getContext = function(postid, append) {
        return $("div#comments-" + postid + (append || ""));
    };
    var initComments = function() {
        $("a[id^='comments-link-']").unbind("click").click(	function() {
            var postid = $(this).attr("id").substr("comments-link-".length);
            var $comments = getContext(postid);
            var formComment = $comments.find('form[id^="add-comment-"]');
            if (formComment.children().length == 0) {
                showAddEditor(postid);
                $comments.removeClass("dno");
                if ($(this).text().indexOf("显示全部") > -1) {
                    showMoreComments(postid, $comments, $(this))
                } else {
                    var ti = 200 + ($('form[id^="add-comment-"] > table').length * 2);
                    var ta = $comments.find("tfoot form textarea");
                    ta.attr("tabindex", ti++);
                    $comments.find("tfoot form input").attr("tabindex",	ti);
                    if (!ta.closest("form").hasClass(".comment-form-expanded")) {
                        ta.focus()
                    }
                }
            } else {
                formComment.show().find("textarea").focus();
            }
            $(this).hide().text("添加回应");
            return false;
        });
    };
    var bindVoteEvent = function(r) {
        $(".comment-up").click(function() {
            voteComment($(this), 2, "comment-up", "comment-up-on", function(t, s) {
                t.closest("tr").siblings("tr").remove();
                t.parent().siblings().children().remove();
                t.parent().siblings().append(b(s.NewScore))
            });
            return false;
        }).hover(function() {
            $(this).addClass("comment-up-on")
        }, function() {
            $(this).removeClass("comment-up-on")
        });

        $("div.comments a.comment-edit, td.comment-summary a.comment-edit").click(function() {
            initEditComment($(this))
        });

        $(".comment-delete").click(	function() {
            if (confirm("确实要删除该评论?")) {
                voteComment($(this), 10, "comment-delete", "delete-tag-hover", function(t, u) {
                    var $tc = t.parents("tr.comment");
                    if (r) {
                        r($tc);
                    }
                    $tc.remove();
                });
            }
        }).hover(function() {
            $(this).addClass("delete-tag-hover");
        }, function() {
            $(this).removeClass("delete-tag-hover");
        });

        if (e) {
            $("tr.comment").find(".comment-delete, a.comment-edit-hide").css("visibility", "visible")
        } else {
            $("tr.comment").hover(function() {
                $(this).addClass("comment-hover").find(".comment-up, .comment-delete, a.comment-edit-hide").css("visibility", "visible")
            },function() {
                $(this).removeClass("comment-hover").find(".comment-up, .comment-delete, a.comment-edit-hide").css("visibility", "hidden")
            })
        }
    };
    var unloadBind = function() {
        $("tr.comment").unbind("mouseenter mouseleave");
        $(".comment-up, .comment-delete, div.comments a.comment-edit").unbind("click mouseenter mouseleave")
    };
    var insertData = function(postid, data) {
        var s = getContext(postid, " > table > tbody");
        var len = s.children().length;
        if (s.children().length > 0) {
            s.children().remove();
        }
        s.append(data);
        unloadBind();
        bindVoteEvent();
        removeLoader();
    };
    var b = function(r) {
        var t = "";
        if (r && r > 0) {
            var s = r < 5 ? "" : r <= 15 ? "warm" : r <= 30 ? "hot"	: "supernova";
            t += '<span title="number of \'great comment\' votes received" class="'	+ s + '">' + r + "</span>"
        }
        return t
    };
    /**
     * 
     * @param {jQuery} container
     * @param {string} submitText
     * @param {boolean} hasCancel
     * @param {function} callback
     * @returns {boolean}
     */
    var showEditor = function(container, submitText, hasCancel, callback) {
        var editor = '<div class="row"><div class="col-md-6"><textarea name="comment" cols="68" rows="3"></textarea></div><div class="col-md-3"><input type="submit" value="'
                + submitText + '"/>' + (hasCancel ? '<a class="edit-comment-cancel">取消</a>' : "")
                + '</div></div><div class="row"><div class="col-md-12"><span class="text-counter"></span><span class="form-error"></span></div></div>';
        container.append(editor);
        if (hasCancel) {
            container.find(".edit-comment-cancel").click(function() {
                cancelEditComment($(this));
            })
        }
        var bCounter = false;
        var setCounterEnable = function(count) {
            bCounter = count;
        };
        var $textarea = container.find("textarea");
        $textarea.charCounter( {
            min : 5,
            max : 600,
            setIsValid : setCounterEnable
        });
        container.submit(function() {
            if (bCounter) {
                setSubmitButtonDisabled(container, true);
                iAsk.helpers.addSpinner(container.find('input[type="submit"]').parent(), {"margin-left" : "10px"});
                callback(container);
            } else {
                container.find("span.text-counter").animate( {opacity : 0}, 100, function() {
                    $(this).animate( {opacity : 1}, 100)
                })
            }
            return false
        });
        iAsk.helpers.bind_submitOnEnterPress(container)
    };
    var showAddEditor = function(postid) {
        showEditor($("#add-comment-" + postid), "添加评论", false, submitAddComment)
    };
    var submitAddComment = function($container) {
        var postid = $container.attr("id").substr("add-comment-".length);
        var removeErrorNotification = function() {
            $(".error-notification").fadeOutAndRemove()
        };
        var $textarea = $container.find("textarea");
        var commentTxt = $textarea.val();
        if (!commentTxt || $.trim(commentTxt) == "") {
            return;
        }
        $.ajax( {
            type : "POST",
            url : $container.attr('action'),
            dataType : "html",
            data : {
                comment : commentTxt,
                fkey : iAsk.options.user.fkey
            },
            success : function(data) {
                removeErrorNotification();
                insertData(postid, data);
                $textarea.val("").keyup();
                setSubmitButtonDisabled($container, false);
                $container.hide().closest(".comments").siblings('a[id^="comments-link"]').show()
            },
            error : function(xhr, textStatus, errorThrown) {
                removeErrorNotification();
                showAjaxError($container,(xhr.responseText && xhr.responseText.length < 100 ? xhr.responseText	: "An error occurred during comment submission"));
                setSubmitButtonDisabled($container, false);
            },
            complete : iAsk.helpers.removeSpinner
        })
    };
    var initEditComment = function(u) {
        var $comment = u.closest("tr.comment");
        var $editComment = $comment.find('form[id^="edit-comment-"]');
        $comment.find("td.comment-actions *").hide();
        $comment.find("td.comment-text > div").hide();
        $comment.find("td:last").addClass("comment-form");
        showEditor($editComment, "好了，提交", true, submitEditComment);
        var $textarea = $editComment.find("textarea");
        $textarea.val($editComment.find("div").text());
        $editComment.show();
        $textarea.focus();
    };
    var submitEditComment = function($form) {
        var commentId = $form.attr("id").substr("edit-comment-".length);
        var w = $form.closest("div.comments, td.comment-summary");
        var postid = w.attr("id").substr("comments-".length);
        var hideNotification = function() {
            $(".error-notification").fadeOut("fast", function() {
                $(this).remove()
            })
        };
        var $ta = $form.find("textarea");
        var txt = $ta.val();
        if (!txt || $.trim(txt) == "") {
            return
        }
        $.ajax( {
            type : "POST",
            url : $form.attr('action'),
            dataType : "html",
            data : {
                comment : txt,
                fkey : iAsk.options.user.fkey
            },
            success : function(data) {
                hideNotification();
                if (e) {
                    w.find("#comment-" + commentId	+ " .comment-text .comment-copy").text(txt);
                    cancelEditComment($form)
                } else {
                    insertData(postid, data)
                }
            },
            error : function(xhr, textStatus, errorThrown) {
                hideNotification();
                showAjaxError($form,(xhr.responseText && xhr.responseText.length < 100 ? xhr.responseText: "An error occurred during comment submission"));
                setSubmitButtonDisabled($container, false);
            },
            complete : iAsk.helpers.removeSpinner
        })
    };
    var cancelEditComment = function(t) {
        var $tr = t.closest("tr.comment");
        var $editComment = $tr.find('form[id^="edit-comment-"]');
        $editComment.children("table").remove();
        $editComment.hide();
        $tr.find("td:last").removeClass("comment-form");
        $tr.find("td.comment-actions *").show();
        $tr.find("td.comment-text > div").show()
    };
    var voteComment = function(commentActionLink, typeId, classname, newClassname, callback) {
        var commentId = commentActionLink.parents("tr.comment").attr("id").substr("comment-".length);
        $("div.error-notification").hide();
        commentActionLink.removeClass(classname).unbind("click mouseenter mouseleave").addClass(newClassname);
        appendLoader(commentActionLink.parent());
        var r = function() {
            commentActionLink.removeClass(newClassname).addClass(classname).click(function() {
                voteComment(commentActionLink, typeId, classname, newClassname, callback)
            })
        };
        var commentActions = "#comment-" + commentId + " td.comment-actions";
        $.ajax( {
            type : "POST",
            url : commentActionLink.attr("href"),
            dataType : "json",
            data : {
                fkey : iAsk.options.user.fkey,
            },
            success : function(data) {
                if (data.Success) {
                    callback(commentActionLink, data)
                } else {
                    iAsk.helpers.showErrorPopup(commentActions, data.Message);
                    r();
                }
            },
            error : function(xhr, B, A) {
                iAsk.helpers.showErrorPopup(commentActions, (xhr.responseText && xhr.responseText.length < 100 ? xhr.responseText : "An error occurred during voting"));
                r();
            }
        });
        removeLoader();
    };
    var showMoreComments = function(postid, $comment, $this) {
        iAsk.helpers.addSpinner($comment);
        $.ajax( {
            type : "GET",
            url : $this.attr('href'),
            dataType : "html",
            success : function(data) {
                insertData(postid, data)
            },
            error : function(xhr, v, u) {
                showAjaxError(getContext(postid), (xhr.responseText && xhr.responseText.length < 100 ? xhr.responseText : "An error has occured while fetching comments"))
            },
            complete : iAsk.helpers.removeSpinner
        })
    };
    return {
        init : function() {
            initComments();
            bindVoteEvent();
            $("form.comment-form-expanded").closest("div.comments").siblings("a.comments-link").click()
        }
    }
}();

function initFadingHelpText() {
    var b = {
        "wmd-input": "#how-to-format",
        'Post_tags': "#how-to-tag",
        'Post_title': "#how-to-title"
    };
    var a = $("#wmd-input, #Post_tags, #Post_title");
    var c = function(d) {
        return $(b[$(d).attr("id")])
    };
    a.focus(function() {
        a.each(function() {
            c(this).hide()
        });
        c(this).wrap('<div class="dno" />').show().parent().fadeIn("slow",
        function() {
            $(this).children().unwrap()
        })
    })
};

var styleCode = (function() {
    function a(c) {
        var b = $("#prettify-lang").text();
        if (b != "") {
            c.addClass(b);
            return true
        }
        return false
    }
    return function() {
        var b = false;
        $("pre code").parent().each(function() {
            if (!$(this).hasClass("prettyprint")) {
                a($(this));
                $(this).addClass("prettyprint");
                b = true
            }
        });
        if (b) {
            prettyPrint();
        }
    }
})();