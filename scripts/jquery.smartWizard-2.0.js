/*
 * SmartWizard 2.0 plugin
 * jQuery Wizard control Plugin
 * by Dipu 
 * 
 * http://www.techlaboratory.net 
 * http://tech-laboratory.blogspot.com
 */

(function (jQuery) {
  jQuery.fn.smartWizard = function (action) {
    var options = jQuery.extend({}, jQuery.fn.smartWizard.defaults, action);
    var args = arguments;

    return this.each(function () {
      var obj = jQuery(this);
      var curStepIdx = options.selected;
      var steps = jQuery("ul > li > a[href^='#step-']", obj); // Get all anchors in this array
      var contentWidth = 0;
      var loader, msgBox, elmActionBar, elmStepContainer, btNext, btPrevious, btFinish;

      elmActionBar = jQuery('.actionBar', obj);
      if (elmActionBar.length == 0) {
        elmActionBar = jQuery('<div></div>').addClass("actionBar");
      }

      msgBox = jQuery('.msgBox', obj);
      if (msgBox.length == 0) {
        msgBox = jQuery('<div class="msgBox"><div class="content"></div><a href="#" class="close">X</a></div>');
        elmActionBar.append(msgBox);
      }

      jQuery('.close', msgBox).click(function () {
        msgBox.fadeOut("normal");
        return false;
      });


      // Method calling logic
      if (!action || action === 'init' || typeof action === 'object') {
        init();
      } else if (action === 'showMessage') {
        //showMessage(Array.prototype.slice.call( args, 1 ));
        var ar = Array.prototype.slice.call(args, 1);
        showMessage(ar[0]);
        return true;
      } else if (action === 'setError') {
        var ar = Array.prototype.slice.call(args, 1);
        setError(ar[0].stepnum, ar[0].iserror);
        return true;
      } else {
        jQuery.error('Method ' + action + ' does not exist');
      }

      function init() {
        var allDivs = obj.children('div'); //$("div", obj);
        obj.children('ul').addClass("anchor");
        allDivs.addClass("content");
        // Create Elements
        loader = jQuery('<div>Loading</div>').addClass("loader");
        elmActionBar = jQuery('<div></div>').addClass("actionBar");
        elmStepContainer = jQuery('<div></div>').addClass("stepContainer");
        btNext = jQuery('<a>' + options.labelNext + '</a>').attr("href", "#").addClass("buttonNext");
        btPrevious = jQuery('<a>' + options.labelPrevious + '</a>').attr("href", "#").addClass("buttonPrevious");
        btFinish = jQuery('<a>' + options.labelFinish + '</a>').attr("href", "#").addClass("buttonFinish");

        // highlight steps with errors
        if (options.errorSteps && options.errorSteps.length > 0) {
          jQuery.each(options.errorSteps, function (i, n) {
            setError(n, true);
          });
        }


        elmStepContainer.append(allDivs);
        elmActionBar.append(loader);
        obj.append(elmStepContainer);
        obj.append(elmActionBar);
        if (options.includeFinishButton) {
          elmActionBar.append(btFinish);
        }
        elmActionBar.append(btNext).append(btPrevious);
        contentWidth = elmStepContainer.width();

        jQuery(btNext).click(function () {
          if (jQuery(this).hasClass('buttonDisabled')) {
            return false;
          }
          doForwardProgress();
          return false;
        });
        jQuery(btPrevious).click(function () {
          if (jQuery(this).hasClass('buttonDisabled')) {
            return false;
          }
          doBackwardProgress();
          return false;
        });
        jQuery(btFinish).click(function () {
          if (!jQuery(this).hasClass('buttonDisabled')) {
            if (jQuery.isFunction(options.onFinish)) {
              if (!options.onFinish.call(this, jQuery(steps))) {
                return false;
              }
            } else {
              var frm = obj.parents('form');
              if (frm && frm.length) {
                frm.submit();
              }
            }
          }

          return false;
        });

        jQuery(steps).bind("click", function (e) {
          if (steps.index(this) == curStepIdx) {
            return false;
          }
          var nextStepIdx = steps.index(this);
          var isDone = steps.eq(nextStepIdx).attr("isDone") - 0;
          if (isDone == 1) {
            LoadContent(nextStepIdx);
          }
          return false;
        });

        // Enable keyboard navigation
        if (options.keyNavigation) {
          jQuery(document).keyup(function (e) {
            if (e.which == 39) { // Right Arrow
              doForwardProgress();
            } else if (e.which == 37) { // Left Arrow
              doBackwardProgress();
            }
          });
        }
        //  Prepare the steps
        prepareSteps();
        // Show the first slected step
        LoadContent(curStepIdx);
      }

      function prepareSteps() {
        if (!options.enableAllSteps) {
          jQuery(steps, obj).removeClass("selected").removeClass("done").addClass("disabled");
          jQuery(steps, obj).attr("isDone", 0);
        } else {
          jQuery(steps, obj).removeClass("selected").removeClass("disabled").addClass("done");
          jQuery(steps, obj).attr("isDone", 1);
        }

        jQuery(steps, obj).each(function (i) {
          jQuery(jQuery(this).attr("href"), obj).hide();
          jQuery(this).attr("rel", i + 1);
        });
      }

      function LoadContent(stepIdx) {
        var selStep = steps.eq(stepIdx);
        var ajaxurl = options.contentURL;
        var hasContent = selStep.data('hasContent');
        stepNum = stepIdx + 1;
        if (ajaxurl && ajaxurl.length > 0) {
          if (options.contentCache && hasContent) {
            showStep(stepIdx);
          } else {
            jQuery.ajax({
              url       :ajaxurl,
              type      :"POST",
              data      :({step_number:stepNum}),
              dataType  :"text",
              beforeSend:function () {
                loader.show();
              },
              error     :function () {
                loader.hide();
              },
              success   :function (res) {
                loader.hide();
                if (res && res.length > 0) {
                  selStep.data('hasContent', true);
                  jQuery(jQuery(selStep, obj).attr("href"), obj).html(res);
                  showStep(stepIdx);
                }
              }
            });
          }
        } else {
          showStep(stepIdx);
        }
      }

      function showStep(stepIdx) {
        var selStep = steps.eq(stepIdx);
        var curStep = steps.eq(curStepIdx);
        if (stepIdx != curStepIdx) {
          if (jQuery.isFunction(options.onLeaveStep)) {
            if (!options.onLeaveStep.call(this, jQuery(curStep))) {
              return false;
            }
          }
        }
        if (options.updateHeight)
          elmStepContainer.height(jQuery(jQuery(selStep, obj).attr("href"), obj).outerHeight());
        if (options.transitionEffect == 'slide') {
          jQuery(jQuery(curStep, obj).attr("href"), obj).slideUp("fast", function (e) {
            jQuery(jQuery(selStep, obj).attr("href"), obj).slideDown("fast");
            curStepIdx = stepIdx;
            SetupStep(curStep, selStep);
          });
        } else if (options.transitionEffect == 'fade') {
          jQuery(jQuery(curStep, obj).attr("href"), obj).fadeOut("fast", function (e) {
            jQuery(jQuery(selStep, obj).attr("href"), obj).fadeIn("fast");
            curStepIdx = stepIdx;
            SetupStep(curStep, selStep);
          });
        } else if (options.transitionEffect == 'slideleft') {
          var nextElmLeft = 0;
          var curElementLeft = 0;
          if (stepIdx > curStepIdx) {
            nextElmLeft1 = contentWidth + 10;
            nextElmLeft2 = 0;
            curElementLeft = 0 - jQuery(jQuery(curStep, obj).attr("href"), obj).outerWidth();
          } else {
            nextElmLeft1 = 0 - jQuery(jQuery(selStep, obj).attr("href"), obj).outerWidth() + 20;
            nextElmLeft2 = 0;
            curElementLeft = 10 + jQuery(jQuery(curStep, obj).attr("href"), obj).outerWidth();
          }
          if (stepIdx == curStepIdx) {
            nextElmLeft1 = jQuery(jQuery(selStep, obj).attr("href"), obj).outerWidth() + 20;
            nextElmLeft2 = 0;
            curElementLeft = 0 - jQuery(jQuery(curStep, obj).attr("href"), obj).outerWidth();
          } else {
            jQuery(jQuery(curStep, obj).attr("href"), obj).animate({left:curElementLeft}, "fast", function (e) {
              jQuery(jQuery(curStep, obj).attr("href"), obj).hide();
            });
          }

          jQuery(jQuery(selStep, obj).attr("href"), obj).css("left", nextElmLeft1);
          jQuery(jQuery(selStep, obj).attr("href"), obj).show();
          jQuery(jQuery(selStep, obj).attr("href"), obj).animate({left:nextElmLeft2}, "fast", function (e) {
            curStepIdx = stepIdx;
            SetupStep(curStep, selStep);
          });
        } else {
          jQuery(jQuery(curStep, obj).attr("href"), obj).hide();
          jQuery(jQuery(selStep, obj).attr("href"), obj).show();
          curStepIdx = stepIdx;
          SetupStep(curStep, selStep);
        }
        return true;
      }

      function SetupStep(curStep, selStep) {
        jQuery(curStep, obj).removeClass("selected");
        jQuery(curStep, obj).addClass("done");

        jQuery(selStep, obj).removeClass("disabled");
        jQuery(selStep, obj).removeClass("done");
        jQuery(selStep, obj).addClass("selected");
        jQuery(selStep, obj).attr("isDone", 1);
        adjustButton();
        if (jQuery.isFunction(options.onShowStep)) {
          if (!options.onShowStep.call(this, jQuery(selStep))) {
            return false;
          }
        }
      }

      function doForwardProgress() {
        var nextStepIdx = curStepIdx + 1;
        if (steps.length <= nextStepIdx) {
          if (!options.cycleSteps) {
            return false;
          }
          nextStepIdx = 0;
        }
        LoadContent(nextStepIdx);
      }

      function doBackwardProgress() {
        var nextStepIdx = curStepIdx - 1;
        if (0 > nextStepIdx) {
          if (!options.cycleSteps) {
            return false;
          }
          nextStepIdx = steps.length - 1;
        }
        LoadContent(nextStepIdx);
      }

      function adjustButton() {
        if (!options.cycleSteps) {
          if (0 >= curStepIdx) {
            jQuery(btPrevious).addClass("buttonDisabled");
          } else {
            jQuery(btPrevious).removeClass("buttonDisabled");
          }
          if ((steps.length - 1) <= curStepIdx) {
            jQuery(btNext).addClass("buttonDisabled");
          } else {
            jQuery(btNext).removeClass("buttonDisabled");
          }
        }
        // Finish Button
        if (!steps.hasClass('disabled') || options.enableFinishButton) {
          jQuery(btFinish).removeClass("buttonDisabled");
        } else {
          jQuery(btFinish).addClass("buttonDisabled");
        }
      }

      function showMessage(msg) {
        jQuery('.content', msgBox).html(msg);
        msgBox.show();
      }

      function setError(stepnum, iserror) {
        if (iserror) {
          jQuery(steps.eq(stepnum - 1), obj).addClass('error')
        } else {
          jQuery(steps.eq(stepnum - 1), obj).removeClass("error");
        }
      }
    });
  };

  // Default Properties and Events
  jQuery.fn.smartWizard.defaults = {
    selected           :0, // Selected Step, 0 = first step
    keyNavigation      :true, // Enable/Disable key navigation(left and right keys are used if enabled)
    enableAllSteps     :false,
    updateHeight       :true,
    transitionEffect   :'fade', // Effect on navigation, none/fade/slide/slideleft
    contentURL         :null, // content url, Enables Ajax content loading
    contentCache       :true, // cache step contents, if false content is fetched always from ajax url
    cycleSteps         :false, // cycle step navigation
    includeFinishButton:true, // whether to show a Finish button
    enableFinishButton :false, // make finish button enabled always
    errorSteps         :[], // Array Steps with errors
    labelNext          :'Next',
    labelPrevious      :'Previous',
    labelFinish        :'Finish',
    onLeaveStep        :null, // triggers when leaving a step
    onShowStep         :null, // triggers when showing a step
    onFinish           :null  // triggers when Finish button is clicked
  };

})(jQuery);
