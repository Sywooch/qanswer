if (typeof(iAsk) == "undefined")
	var iAsk = {};
iAsk.heart = {};
iAsk.editor = {};

iAsk.heart = (function() {
    var activateHeartManagerList = [],
    currentHeartManager,
    heartManagerList = [],
    gPostData,
    intialInterval = 45,
    delay = 0,
    gOffset,
    timeoutHandle;
    
    function startHeartBeat(delta) {
        var interval = (typeof delta === "number") ? delta: (intialInterval + delay) * 1000;
        if (gOffset) {
            interval = Math.max(interval, 6000 - (new Date().getTime() - gOffset))
        }
        if (timeoutHandle) {
            clearTimeout(timeoutHandle)
        }
        timeoutHandle = setTimeout(heartRunning, interval)
    }
    function heartRunning() {
        timeoutHandle = null;
        if (!heartManagerList.length) {
            saveHeartBeat();
        }
        if (!heartManagerList.length) {
            startHeartBeat();
            return
        }
        var _heartManager = heartManagerList.shift();
        if (_heartManager.checkActive()) {
            _heartManager.beat()
        } else {
            startHeartBeat()
        }
    }
    function saveHeartBeat() {
        var _activeHeartManager;
        heartManagerList = [];
        if (currentHeartManager && !currentHeartManager.isDisabled && currentHeartManager.beatCount < 30) {
            heartManagerList.push(currentHeartManager)
        }
        for (var i = 0; i < activateHeartManagerList.length; i++) {
            _activeHeartManager = activateHeartManagerList[i];
            if (_activeHeartManager != currentHeartManager && !_activeHeartManager.isDisabled && _activeHeartManager.beatCount < 30) {
                heartManagerList.push(_activeHeartManager)
            }
        }
        intialInterval = Math.max(15, Math.min(45, 60 / (heartManagerList.length || 1)))
    }
    
    function heartManager() {};
    
    heartManager.prototype = {
        activate: function() {
            currentHeartManager = this;
            if (this.isActive) {
                return;
            }
            this.isActive = true;
            this.beatCount = 0;
            activateHeartManagerList.push(this);
            if (activateHeartManagerList.length === 1) {
                startHeartBeat();
            }
        },
        checkActive: function() {
            if (!this.isActive || this.isDisabled) {
                return false
            }
            if (!this.jTextarea.closest("body").length) {
                delete this.jTextarea;
                this.isDisabled = true;
                return false
            }
            return true
        },
        beat: function(x) {
            var that = this,
            jTextarea = {
                type: "POST",
                url: iAsk.editor.heartBeatUrl +"/?type="+this.type,
                dataType: "json",
                data: {}
            };
            if (!x) {
                jTextarea.success = function(A) {
                    that.success(A)
                };
                jTextarea.error = function() {
                    that.error()
                };
                jTextarea.complete = function() {
                    that.complete()
                }
            }
            if (this.shouldSendDraft()) {
                var postData = {
                    text: this.jTextarea.val()
                };
                if (this.type === "ask") {
                    postData.title = $("#Post_title").val();
                    postData.tagnames = $("#Post_tags").val()
                }
                if (this.type === "answer") {
                	postData.postid = this.postId;
                }
                if (!gPostData || gPostData.heart !== this || gPostData.title !== postData.title || gPostData.tagnames !== postData.tagnames || gPostData.text !== postData.text) {
                    jTextarea.data = postData;
                    gPostData = {
                        heart: this,
                        title: postData.title,
                        tagnames: postData.tagnames,
                        text: postData.text,
                    }
                }
            }
            if (x && !("text" in jTextarea.data)) {
                return $.Deferred().resolve().promise()
            }
//            if (this.type === "answer") {
//                var v = $("#answers-header .answers-subheader h2").text().replace(/ answers?/i, "") || "0";
//                jTextarea.data.clientCount = v
//            }
            gOffset = new Date().getTime();
            return $.ajax(jTextarea).promise()
        },
        shouldSendDraft: function() {
            return this.type !== "edit" && currentHeartManager === this
        },
        success: function(data) {
            if (data.draftSaved) {
                processDraftSaved(this.jTextarea)
            }
            this.beatCount++;
            delay = 0
        },
        error: function() {
            $("#draft-saved").hide();
            if (currentHeartManager === this) {
                heartManagerList.unshift(this)
            }
            delay = Math.random() * 10
        },
        complete: function() {
            startHeartBeat()
        }
    };
    var processDraftSaved = function(jTextarea) {
        var $draftSaved = $("#draft-saved");
        var hideDraftSaved = function() {
            $draftSaved.text("草稿已保存").fadeIn("fast")
        };
        if ($draftSaved.is(":visible")) {
            $draftSaved.fadeOut("fast", hideDraftSaved)
        } else {
            hideDraftSaved();
        }
        var onKeypress = function(e) {
            if (e.which != 115 || !e.ctrlKey || e.shiftKey || e.altKey) {
                jTextarea.unbind("keypress", onKeypress);
                $("#draft-saved").fadeOut("fast")
            }
        };
        jTextarea.bind("keypress", onKeypress)
    };
    function _addHeart(heartbeatType, jTextarea) {
        var heartBeat = new heartManager(), postId;
        heartBeat.type = heartbeatType;
        heartBeat.jTextarea = jTextarea;
        switch (heartbeatType) {
        case "ask":
            postId = 0;
            break;
        case "answer":
            postId = location.href.match(/\/questions\/(\d+)/i)[1];
            break;
        }
        heartBeat.postId = postId;
        jTextarea.keypress(function() {
            heartBeat.activate();
        })
    }
    function _ensureDraftSaved(v) {
        if (!currentHeartManager || !currentHeartManager.checkActive()) {
            v();
            return
        }
        currentHeartManager.beat().done(v)
    }
    return {
        addHeart: _addHeart,
        ensureDraftSaved: _ensureDraftSaved,
    }
})();

iAsk.editor = (function() {
	var _init = function(requestUrl,type) {
		if (typeof type == undefined) {
			type = 'ask';
		}
		iAsk.editor.heartBeatUrl = requestUrl;
		iAsk.heart.addHeart(type, $("#wmd-input"));
		$("#ask-form").submit(function(){
			iAsk.navPrevention.stop();
		});
	};
    return {
        init: _init
    }
})();


iAsk.navPrevention = (function() {
    var jElement, texts;
    var b = function() {
        var exist = false;
        jElement.each(function(i) {
            exist = exist || ($(this).val() !== texts[i])
        });
        return exist
    };
    var bindUnload = function(f) {
        window.onbeforeunload = f ?
        function() {
            if (jElement && b()) {
                return f
            }
        }: null
    };
    var onKeypress = function(f) {
        bindUnload("You have started writing or editing a post.")
    };
    return {
        init: function(_jElement) {
            jElement = _jElement.one("keypress", onKeypress);
            texts = [];
            _jElement.each(function() {
                texts.push($(this).val())
            })
        },
        stop: function() {
            if (!jElement) {
                return
            }
            jElement.unbind("keypress", onKeypress);
            bindUnload(null);
            jElement = null
        },
        confirm: function(message) {
            if (jElement && b()) {
                return confirm(message)
            }
            return true
        }
    }
})(); 