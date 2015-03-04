// Global array for entire page
// Used to store tab IDs to prevent more than one tab using the same ID
var tabsIdList = [];

// Main tabs object
// Can be called with: new vpTabs(element(jquery obj)[, options(obj)][, activeTab(int)]) 
(function($, tabsIdList) {
  'use strict';
  
  function Tabs(el, options, activeTab) {

    // Bind element argument to object element
    this.el = el;

    // Extends defaults-options with user defined options
    this.options = $.extend( this.defaults, options || {} );

    // Array containing reference to all tab links
    this.tabs = [];
    // Array containing reference to all content blocks
    this.content = [];
    // Int storing the active tab
    this.activeTab = activeTab ? activeTab : 0;

    // Let the fun begin!
    this._init();
  }

  Tabs.prototype = {
    // Default options
    // Override in call "$(selector).tabs({option1: value1, option2: value2});" or "new vpTabs($(selector),{option1: value1, option2: value2});"
    defaults : {
        // Classes
        tabsBarClass:           'tabs__bar',
        tabsBarItemClass:       'tabs__bar__item',
        tabsBarItemActiveClass: 'tabs__bar__item--active',
        tabsBarItemLink:        'tabs__bar__item__link',
        tabsBarItemJSLink:      'js-tab-link',
        tabsBarItemJSLinkClass: '.js-tab-link',
        tabsBarItemLinkActive:  'tabs__bar__item__link--active',
        contentActive:          'tabs__content__block--active',
        contentHidden:          'tabs__content__block--hidden',
        tabsContentHeaderClass: '.tabs__content__header',
        tabsContentHeaderActive:'tabs__content__header--active' 
    },

    _init : function() {
      // Var to keep tab this-reference
      // Var shortcut to elelemt
      // Create new <ul>-list and add a class to it
      var tab = this,
          tabElm = tab.el,
          tabsElm = $('<ul></ul>').addClass( tab.options.tabsBarClass );

      // Search through each content list item.
      // For each list element in the content-list; create a corresponding tab in the tabs
      // We separate html-classes used for javascript from classes for css by prepending 'js-' and '[javascript-file-name]-'.
      // This is to make it clear what function a class has so that you can know what will happen if it is removed
      $(tabElm).find('.js-tabs-tab').each( function( index ){

        // 1. Create at tabID from the list-contents header text (make it lowercase and replace unwantet chars)
        // 2. Clone the tabID so we can have appen a number in case of multiple tabs with same name
        // 3. Create tab link element, add classes and text
        // 4. Create tab <li>-element and add classes
        // 5. Tab id, to be used in case of multiple tabs with same name
        // 6. Shortcut var to content node
        var tabIdOrig = encodeURIComponent( $(this).text().toLowerCase().replace(' ', '-') ),
            tabId = tabIdOrig,
            tabLinkElm = $('<a></a>').addClass( tab.options.tabsBarItemLink + ' ' + tab.options.tabsBarItemJSLink ).text( $(this).text() ),
            tabElm = $('<li></li>').addClass( tab.options.tabsBarItemClass ),
            tabIdNum = 1,
            contentElm = $(this).next();

        // There might be more tabs with the same name, especially if there are many tab blocks on the same page
        // This would cause conflicts and inconsisten behavior
        // We check if the tabId is already in the tabsIdList array,
        // if it is we append an increasing number untill it no longer finds a match
        while( $.inArray(tabId, tabsIdList) !== -1 ) {
          tabId = tabIdOrig + tabIdNum;
          tabIdNum++;
        }

        // We append the validated tabId to the array so that no other tabs can share the same ID
        tabsIdList.push( tabId );

        // We add the Id as an internal link hraf to the tab link
        // We also add the index to the element as data, this will be used to _setActiveTab(index) when the tab is clicked
        tabLinkElm.attr('href', function(){ return '#' + tabId }).data('index', index);

        // We append the now complene link element to the <li>-element
        tabElm.append( tabLinkElm );
        $(this).wrap( $('<a></a>').addClass( tab.options.tabsBarItemJSLink ).attr('href', function(){ return '#' + tabId }).data('index', index) );

        // Add link to <li> element
        tabElm.append(tabLinkElm);
        
        // Add liste element to <ul>-element
        tabsElm.append(tabElm);

        // $(this) is the tab content header. The next element is the tab content body (the part we show/hide)
        // We need the content body to have an ID mirroring the ID-link in the corresponding tab
        // For accessibility reasons we add tabindex -1 to the element so it can receive focus in Chrome
        contentElm.attr('id', tabId).attr('tabindex', '-1').addClass( tab.options.contentHidden );

        tab.tabs.push(tabLinkElm);
        tab.content.push(contentElm);

        // If current tab id is in url-hash, set that to active tab
        if( location.hash.substr(1) === tabId ) {
          tab.activeTab = index;
        }

      });


      tab._setActiveTab(this.activeTab, true);

      // Insert the new list first in the tabs container, before the body content list
      $(tabElm).prepend(tabsElm);

      // Add class to signal that tabs is ready. We use this in CSS to show tabs and hide content headers (on desktop devices anyways)
      // Bind click on tab links to show and hide content
      $(tabElm).addClass('tabs--ready').on('click', tab.options.tabsBarItemJSLinkClass, function(){

        // The hash-link from the clicked tab
        var hash = $(this).attr('href');
        //console.log($(this).data('index'));
        
        // Append tab to url (in case user bookmarks or shares it)
        if(history.pushState) {
          history.pushState(null, null, hash);
        }
        else {
          location.hash = hash;
        }

        tab._setActiveTab($(this).data('index'));

        // Stop link from triggering
        return false;
      });
    },
    _setActiveTab : function( currentTabIndex, isInit ) {
      var isInit = isInit ? isInit : false,
          activeTabIndex = this.activeTab,
          activeTab = this.tabs[ activeTabIndex ],
          activeTabContent = this.content[ activeTabIndex ],
          activeTabContentHeader = activeTabContent.prev().children( this.options.tabsContentHeaderClass ),
          currentTab = this.tabs[ currentTabIndex ],
          currentContent = this.content[ currentTabIndex ],
          currentContentHeader = currentContent.prev().children( this.options.tabsContentHeaderClass );

      // If not already active, or if this is the first time setup and it shoud be active
      if( isInit || currentTabIndex !== activeTabIndex ) {

        // No need to remove classes on init
        if( !isInit ) {
          activeTab.removeClass( this.options.tabsBarItemLinkActive );
          activeTabContent.removeClass( this.options.contentActive ).addClass( this.options.contentHidden );
          activeTabContentHeader.removeClass( this.options.tabsContentHeaderActive );
        }

        currentTab.addClass( this.options.tabsBarItemLinkActive );
        currentContent.removeClass( this.options.contentHidden ).addClass( this.options.contentActive );
        currentContentHeader.addClass( this.options.tabsContentHeaderActive );

        // Upadate which tab is active
        this.activeTab = currentTabIndex;

        if( !isInit && $(window).scrollTop() > currentContent.offset().top ) {
          //window.scrollTo( 0, Math.ceil( currentContent.offset().top ) );
          $('html, body').animate({ scrollTop: currentContent.offset().top }, 500);
        }
      }
    }
  }

  // add to global namespace
  window.vpTabs = Tabs;
})(jQuery, tabsIdList);

// Create a jquery method which creates a new vpTabs object for each instance
(function($) {
  'use strict';

  $.fn.tabs = function( options, activeTab ) {
    return this.each( function() {
      new vpTabs($(this), options, activeTab);
    });
  };
})(jQuery);

$('.js-tabs').tabs();

