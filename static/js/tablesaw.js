/*tablesaw.js*/
/*! Tablesaw - v2.0.2 - 2015-10-28
* https://github.com/filamentgroup/tablesaw
* Copyright (c) 2015 Filament Group; Licensed  */
/*
* tablesaw: A set of plugins for responsive tables
* Stack and Column Toggle tables
* Copyright (c) 2013 Filament Group, Inc.
* MIT License
*/

if( typeof Tablesaw === "undefined" ) {
    Tablesaw = {
        i18n: {
            modes: [ 'Stack', 'Swipe', 'Toggle' ],
            columns: 'Col<span class=\"a11y-sm\">umn</span>s',
            columnBtnText: 'Columns',
            columnsDialogError: 'No eligible columns.',
            sort: 'Sort'
        },
        // cut the mustard
        mustard: 'querySelector' in document &&
        ( !window.blackberry || window.WebKitPoint ) &&
        !window.operamini
    };
}
if( !Tablesaw.config ) {
    Tablesaw.config = {};
}
if( Tablesaw.mustard ) {
    jQuery( document.documentElement ).addClass( 'tablesaw-enhanced' );
}

;(function( $ ) {
    var pluginName = "table",
        classes = {
            toolbar: "tablesaw-bar"
        },
        events = {
            create: "tablesawcreate",
            destroy: "tablesawdestroy",
            refresh: "tablesawrefresh"
        },
        defaultMode = "stack",
        initSelector = "table[data-tablesaw-mode],table[data-tablesaw-sortable]";

    var Table = function( element ) {
        if( !element ) {
            throw new Error( "Tablesaw requires an element." );
        }

        this.table = element;
        this.$table = $( element );

        this.mode = this.$table.attr( "data-tablesaw-mode" ) || defaultMode;

        this.init();
    };

    Table.prototype.init = function() {
        // assign an id if there is none
        if ( !this.$table.attr( "id" ) ) {
            this.$table.attr( "id", pluginName + "-" + Math.round( Math.random() * 10000 ) );
        }

        this.createToolbar();

        var colstart = this._initCells();

        this.$table.trigger( events.create, [ this, colstart ] );
    };

    Table.prototype._initCells = function() {
        var colstart,
            thrs = this.table.querySelectorAll( "thead tr" ),
            self = this;

        $( thrs ).each( function(){
            var coltally = 0;

            $( this ).children().each( function(){
                var span = parseInt( this.getAttribute( "colspan" ), 10 ),
                    sel = ":nth-child(" + ( coltally + 1 ) + ")";

                colstart = coltally + 1;

                if( span ){
                    for( var k = 0; k < span - 1; k++ ){
                        coltally++;
                        sel += ", :nth-child(" + ( coltally + 1 ) + ")";
                    }
                }

                // Store "cells" data on header as a reference to all cells in the same column as this TH
                this.cells = self.$table.find("tr").not( thrs[0] ).not( this ).children().filter( sel );
                coltally++;
            });
        });

        return colstart;
    };

    Table.prototype.refresh = function() {
        this._initCells();

        this.$table.trigger( events.refresh );
    };

    Table.prototype.createToolbar = function() {
        // Insert the toolbar
        // TODO move this into a separate component
        var $toolbar = this.$table.prev().filter( '.' + classes.toolbar );
        if( !$toolbar.length ) {
            $toolbar = $( '<div>' )
                .addClass( classes.toolbar )
                .insertBefore( this.$table );
        }
        this.$toolbar = $toolbar;

        if( this.mode ) {
            this.$toolbar.addClass( 'mode-' + this.mode );
        }
    };

    Table.prototype.destroy = function() {
        // Donâ€™t remove the toolbar. Some of the table features are not yet destroy-friendly.
        this.$table.prev().filter( '.' + classes.toolbar ).each(function() {
            this.className = this.className.replace( /\bmode\-\w*\b/gi, '' );
        });

        var tableId = this.$table.attr( 'id' );
        $( document ).unbind( "." + tableId );
        $( window ).unbind( "." + tableId );

        // other plugins
        this.$table.trigger( events.destroy, [ this ] );

        this.$table.removeAttr( 'data-tablesaw-mode' );

        this.$table.removeData( pluginName );
    };

    // Collection method.
    $.fn[ pluginName ] = function() {
        return this.each( function() {
            var $t = $( this );

            if( $t.data( pluginName ) ){
                return;
            }

            var table = new Table( this );
            $t.data( pluginName, table );
        });
    };

    $( document ).on( "enhance.tablesaw", function( e ) {
        // Cut the mustard
        if( Tablesaw.mustard ) {
            $( e.target ).find( initSelector )[ pluginName ]();
        }
    });

}( jQuery ));

;(function( win, $, undefined ){

    var classes = {
        stackTable: 'tablesaw-stack',
        cellLabels: 'tablesaw-cell-label',
        cellContentLabels: 'tablesaw-cell-content'
    };

    var data = {
        obj: 'tablesaw-stack'
    };

    var attrs = {
        labelless: 'data-tablesaw-no-labels',
        hideempty: 'data-tablesaw-hide-empty'
    };

    var Stack = function( element ) {

        this.$table = $( element );

        this.labelless = this.$table.is( '[' + attrs.labelless + ']' );
        this.hideempty = this.$table.is( '[' + attrs.hideempty + ']' );

        if( !this.labelless ) {
            // allHeaders references headers, plus all THs in the thead, which may include several rows, or not
            this.allHeaders = this.$table.find( "th" );
        }

        this.$table.data( data.obj, this );
    };

    Stack.prototype.init = function( colstart ) {
        this.$table.addClass( classes.stackTable );

        if( this.labelless ) {
            return;
        }

        // get headers in reverse order so that top-level headers are appended last
        var reverseHeaders = $( this.allHeaders );
        var hideempty = this.hideempty;

        // create the hide/show toggles
        reverseHeaders.each(function(){
            var $t = $( this ),
                $cells = $( this.cells ).filter(function() {
                    return !$( this ).parent().is( "[" + attrs.labelless + "]" ) && ( !hideempty || !$( this ).is( ":empty" ) );
                }),
                hierarchyClass = $cells.not( this ).filter( "thead th" ).length && " tablesaw-cell-label-top",
                // TODO reduce coupling with sortable
                $sortableButton = $t.find( ".tablesaw-sortable-btn" ),
                html = $sortableButton.length ? $sortableButton.html() : $t.html();

            if( html !== "" ){
                if( hierarchyClass ){
                    var iteration = parseInt( $( this ).attr( "colspan" ), 10 ),
                        filter = "";

                    if( iteration ){
                        filter = "td:nth-child("+ iteration +"n + " + ( colstart ) +")";
                    }
                    $cells.filter( filter ).prepend( "<b class='" + classes.cellLabels + hierarchyClass + "'>" + html + "</b>"  );
                } else {
                    $cells.wrapInner( "<span class='" + classes.cellContentLabels + "'></span>" );
                    $cells.prepend( "<b class='" + classes.cellLabels + "'>" + html + "</b>"  );
                }
            }
        });
    };

    Stack.prototype.destroy = function() {
        this.$table.removeClass( classes.stackTable );
        this.$table.find( '.' + classes.cellLabels ).remove();
        this.$table.find( '.' + classes.cellContentLabels ).each(function() {
            $( this ).replaceWith( this.childNodes );
        });
    };

    // on tablecreate, init
    $( document ).on( "tablesawcreate", function( e, Tablesaw, colstart ){
        if( Tablesaw.mode === 'stack' ){
            var table = new Stack( Tablesaw.table );
            table.init( colstart );
        }

    } );

    $( document ).on( "tablesawdestroy", function( e, Tablesaw ){

        if( Tablesaw.mode === 'stack' ){
            $( Tablesaw.table ).data( data.obj ).destroy();
        }

    } );

}( this, jQuery ));
;(function( $ ) {
    var pluginName = "tablesawbtn",
        methods = {
            _create: function(){
                return $( this ).each(function() {
                    $( this )
                        .trigger( "beforecreate." + pluginName )
                        [ pluginName ]( "_init" )
                        .trigger( "create." + pluginName );
                });
            },
            _init: function(){
                var oEl = $( this ),
                    sel = this.getElementsByTagName( "select" )[ 0 ];

                if( sel ) {
                    $( this )
                        .addClass( "btn-select" )
                        [ pluginName ]( "_select", sel );
                }
                return oEl;
            },
            _select: function( sel ) {
                var update = function( oEl, sel ) {
                    var opts = $( sel ).find( "option" ),
                        label, el, children;

                    opts.each(function() {
                        var opt = this;
                        if( opt.selected ) {
                            label = document.createTextNode( opt.text );
                        }
                    });

                    children = oEl.childNodes;
                    if( opts.length > 0 ){
                        for( var i = 0, l = children.length; i < l; i++ ) {
                            el = children[ i ];

                            if( el && el.nodeType === 3 ) {
                                oEl.replaceChild( label, el );
                            }
                        }
                    }
                };

                update( this, sel );
                $( this ).bind( "change refresh", function() {
                    update( this, sel );
                });
            }
        };

    // Collection method.
    $.fn[ pluginName ] = function( arrg, a, b, c ) {
        return this.each(function() {

            // if it's a method
            if( arrg && typeof( arrg ) === "string" ){
                return $.fn[ pluginName ].prototype[ arrg ].call( this, a, b, c );
            }

            // don't re-init
            if( $( this ).data( pluginName + "active" ) ){
                return $( this );
            }

            // otherwise, init

            $( this ).data( pluginName + "active", true );
            $.fn[ pluginName ].prototype._create.call( this );
        });
    };

    // add methods
    $.extend( $.fn[ pluginName ].prototype, methods );

}( jQuery ));
;(function( win, $, undefined ){

    var ColumnToggle = function( element ) {

        this.$table = $( element );

        this.classes = {
            columnToggleTable: 'tablesaw-columntoggle',
            columnBtnContain: 'tablesaw-columntoggle-btnwrap tablesaw-advance',
            columnBtn: 'tablesaw-columntoggle-btn tablesaw-nav-btn down',
            popup: 'tablesaw-columntoggle-popup',
            priorityPrefix: 'tablesaw-priority-',
            // TODO duplicate class, also in tables.js
            toolbar: 'tablesaw-bar'
        };

        // Expose headers and allHeaders properties on the widget
        // headers references the THs within the first TR in the table
        this.headers = this.$table.find( 'tr:first > th' );

        this.$table.data( 'tablesaw-coltoggle', this );
    };

    ColumnToggle.prototype.init = function() {

        var tableId,
            id,
            $menuButton,
            $popup,
            $menu,
            $btnContain,
            self = this;

        this.$table.addClass( this.classes.columnToggleTable );

        tableId = this.$table.attr( "id" );
        id = tableId + "-popup";
        $btnContain = $( "<div class='" + this.classes.columnBtnContain + "'></div>" );
        $menuButton = $( "<a href='#" + id + "' class='btn btn-micro " + this.classes.columnBtn +"' data-popup-link>" +
            "<span>" + Tablesaw.i18n.columnBtnText + "</span></a>" );
        $popup = $( "<div class='dialog-table-coltoggle " + this.classes.popup + "' id='" + id + "'></div>" );
        $menu = $( "<div class='btn-group'></div>" );

        var hasNonPersistentHeaders = false;
        $( this.headers ).not( "td" ).each( function() {
            var $this = $( this ),
                priority = $this.attr("data-tablesaw-priority"),
                $cells = self.$getCells( this );

            if( priority && priority !== "persist" ) {
                $cells.addClass( self.classes.priorityPrefix + priority );

                $("<label><input type='checkbox' checked>" + $this.text() + "</label>" )
                    .appendTo( $menu )
                    .children( 0 )
                    .data( "tablesaw-header", this );

                hasNonPersistentHeaders = true;
            }
        });

        if( !hasNonPersistentHeaders ) {
            $menu.append( '<label>' + Tablesaw.i18n.columnsDialogError + '</label>' );
        }

        $menu.appendTo( $popup );

        // bind change event listeners to inputs - TODO: move to a private method?
        $menu.find( 'input[type="checkbox"]' ).on( "change", function(e) {
            var checked = e.target.checked;

            self.$getCellsFromCheckbox( e.target )
                .toggleClass( "tablesaw-cell-hidden", !checked )
                .toggleClass( "tablesaw-cell-visible", checked );

            self.$table.trigger( 'tablesawcolumns' );
        });

        $menuButton.appendTo( $btnContain );
        $btnContain.appendTo( this.$table.prev().filter( '.' + this.classes.toolbar ) );

        var closeTimeout;
        function openPopup() {
            $btnContain.addClass( 'visible' );
            $menuButton.removeClass( 'down' ).addClass( 'up' );

            $( document ).unbind( 'click.' + tableId, closePopup );

            window.clearTimeout( closeTimeout );
            closeTimeout = window.setTimeout(function() {
                $( document ).one( 'click.' + tableId, closePopup );
            }, 15 );
        }

        function closePopup( event ) {
            // Click came from inside the popup, ignore.
            if( event && $( event.target ).closest( "." + self.classes.popup ).length ) {
                return;
            }

            $( document ).unbind( 'click.' + tableId );
            $menuButton.removeClass( 'up' ).addClass( 'down' );
            $btnContain.removeClass( 'visible' );
        }

        $menuButton.on( "click.tablesaw", function( event ) {
            event.preventDefault();

            if( !$btnContain.is( ".visible" ) ) {
                openPopup();
            } else {
                closePopup();
            }
        });

        $popup.appendTo( $btnContain );

        this.$menu = $menu;

        $(window).on( "resize." + tableId, function(){
            self.refreshToggle();
        });

        this.refreshToggle();
    };

    ColumnToggle.prototype.$getCells = function( th ) {
        return $( th ).add( th.cells );
    };

    ColumnToggle.prototype.$getCellsFromCheckbox = function( checkbox ) {
        var th = $( checkbox ).data( "tablesaw-header" );
        return this.$getCells( th );
    };

    ColumnToggle.prototype.refreshToggle = function() {
        var self = this;
        this.$menu.find( "input" ).each( function() {
            this.checked = self.$getCellsFromCheckbox( this ).eq( 0 ).css( "display" ) === "table-cell";
        });
    };

    ColumnToggle.prototype.refreshPriority = function(){
        var self = this;
        $(this.headers).not( "td" ).each( function() {
            var $this = $( this ),
                priority = $this.attr("data-tablesaw-priority"),
                $cells = $this.add( this.cells );

            if( priority && priority !== "persist" ) {
                $cells.addClass( self.classes.priorityPrefix + priority );
            }
        });
    };

    ColumnToggle.prototype.destroy = function() {
        // table toolbars, document and window .tableId events
        // removed in parent tables.js destroy method

        this.$table.removeClass( this.classes.columnToggleTable );
        this.$table.find( 'th, td' ).each(function() {
            var $cell = $( this );
            $cell.removeClass( 'tablesaw-cell-hidden' )
                .removeClass( 'tablesaw-cell-visible' );

            this.className = this.className.replace( /\bui\-table\-priority\-\d\b/g, '' );
        });
    };

    // on tablecreate, init
    $( document ).on( "tablesawcreate", function( e, Tablesaw ){

        if( Tablesaw.mode === 'columntoggle' ){
            var table = new ColumnToggle( Tablesaw.table );
            table.init();
        }

    } );

    $( document ).on( "tablesawdestroy", function( e, Tablesaw ){
        if( Tablesaw.mode === 'columntoggle' ){
            $( Tablesaw.table ).data( 'tablesaw-coltoggle' ).destroy();
        }
    } );

}( this, jQuery ));
;(function( win, $, undefined ){

    $.extend( Tablesaw.config, {
        swipe: {
            horizontalThreshold: 15,
            verticalThreshold: 30
        }
    });

    function isIE8() {
        var div = document.createElement('div'),
            all = div.getElementsByTagName('i');

        div.innerHTML = '<!--[if lte IE 8]><i></i><![endif]-->';

        return !!all.length;
    }

    function createSwipeTable( $table ){

        var $btns = $( "<div class='tablesaw-advance'></div>" ),
            $prevBtn = $( "<a href='#' class='tablesaw-nav-btn btn btn-micro left' title='Previous Column'></a>" ).appendTo( $btns ),
            $nextBtn = $( "<a href='#' class='tablesaw-nav-btn btn btn-micro right' title='Next Column'></a>" ).appendTo( $btns ),
            hideBtn = 'disabled',
            persistWidths = 'tablesaw-fix-persist',
            $headerCells = $table.find( "thead th" ),
            $headerCellsNoPersist = $headerCells.not( '[data-tablesaw-priority="persist"]' ),
            headerWidths = [],
            $head = $( document.head || 'head' ),
            tableId = $table.attr( 'id' ),
            // TODO switch this to an nth-child feature test
            supportsNthChild = !isIE8();

        if( !$headerCells.length ) {
            throw new Error( "tablesaw swipe: no header cells found. Are you using <th> inside of <thead>?" );
        }

        // Calculate initial widths
        $table.css('width', 'auto');
        $headerCells.each(function() {
            headerWidths.push( $( this ).outerWidth() );
        });
        $table.css( 'width', '' );

        $btns.appendTo( $table.prev().filter( '.tablesaw-bar' ) );

        $table.addClass( "tablesaw-swipe" );

        if( !tableId ) {
            tableId = 'tableswipe-' + Math.round( Math.random() * 10000 );
            $table.attr( 'id', tableId );
        }

        function $getCells( headerCell ) {
            return $( headerCell.cells ).add( headerCell );
        }

        function showColumn( headerCell ) {
            $getCells( headerCell ).removeClass( 'tablesaw-cell-hidden' );
        }

        function hideColumn( headerCell ) {
            $getCells( headerCell ).addClass( 'tablesaw-cell-hidden' );
        }

        function persistColumn( headerCell ) {
            $getCells( headerCell ).addClass( 'tablesaw-cell-persist' );
        }

        function isPersistent( headerCell ) {
            return $( headerCell ).is( '[data-tablesaw-priority="persist"]' );
        }

        function unmaintainWidths() {
            $table.removeClass( persistWidths );
            $( '#' + tableId + '-persist' ).remove();
        }

        function maintainWidths() {
            var prefix = '#' + tableId + '.tablesaw-swipe ',
                styles = [],
                tableWidth = $table.width(),
                hash = [],
                newHash;

            $headerCells.each(function( index ) {
                var width;
                if( isPersistent( this ) ) {
                    width = $( this ).outerWidth();

                    // Only save width on non-greedy columns (take up less than 75% of table width)
                    if( width < tableWidth * 0.75 ) {
                        hash.push( index + '-' + width );
                        styles.push( prefix + ' .tablesaw-cell-persist:nth-child(' + ( index + 1 ) + ') { width: ' + width + 'px; }' );
                    }
                }
            });
            newHash = hash.join( '_' );

            $table.addClass( persistWidths );

            var $style = $( '#' + tableId + '-persist' );
            // If style element not yet added OR if the widths have changed
            if( !$style.length || $style.data( 'hash' ) !== newHash ) {
                // Remove existing
                $style.remove();

                if( styles.length ) {
                    $( '<style>' + styles.join( "\n" ) + '</style>' )
                        .attr( 'id', tableId + '-persist' )
                        .data( 'hash', newHash )
                        .appendTo( $head );
                }
            }
        }

        function getNext(){
            var next = [],
                checkFound;

            $headerCellsNoPersist.each(function( i ) {
                var $t = $( this ),
                    isHidden = $t.css( "display" ) === "none" || $t.is( ".tablesaw-cell-hidden" );

                if( !isHidden && !checkFound ) {
                    checkFound = true;
                    next[ 0 ] = i;
                } else if( isHidden && checkFound ) {
                    next[ 1 ] = i;

                    return false;
                }
            });

            return next;
        }

        function getPrev(){
            var next = getNext();
            return [ next[ 1 ] - 1 , next[ 0 ] - 1 ];
        }

        function nextpair( fwd ){
            return fwd ? getNext() : getPrev();
        }

        function canAdvance( pair ){
            return pair[ 1 ] > -1 && pair[ 1 ] < $headerCellsNoPersist.length;
        }

        function matchesMedia() {
            var matchMedia = $table.attr( "data-tablesaw-swipe-media" );
            return !matchMedia || ( "matchMedia" in win ) && win.matchMedia( matchMedia ).matches;
        }

        function fakeBreakpoints() {
            if( !matchesMedia() ) {
                return;
            }

            var extraPaddingPixels = 20,
                containerWidth = $table.parent().width(),
                persist = [],
                sum = 0,
                sums = [],
                visibleNonPersistantCount = $headerCells.length;

            $headerCells.each(function( index ) {
                var $t = $( this ),
                    isPersist = $t.is( '[data-tablesaw-priority="persist"]' );

                persist.push( isPersist );

                sum += headerWidths[ index ] + ( isPersist ? 0 : extraPaddingPixels );
                sums.push( sum );

                // is persistent or is hidden
                if( isPersist || sum > containerWidth ) {
                    visibleNonPersistantCount--;
                }
            });

            var needsNonPersistentColumn = visibleNonPersistantCount === 0;

            $headerCells.each(function( index ) {
                if( persist[ index ] ) {

                    // for visual box-shadow
                    persistColumn( this );
                    return;
                }

                if( sums[ index ] <= containerWidth || needsNonPersistentColumn ) {
                    needsNonPersistentColumn = false;
                    showColumn( this );
                } else {
                    hideColumn( this );
                }
            });

            if( supportsNthChild ) {
                unmaintainWidths();
            }
            $table.trigger( 'tablesawcolumns' );
        }

        function advance( fwd ){
            var pair = nextpair( fwd );
            if( canAdvance( pair ) ){
                if( isNaN( pair[ 0 ] ) ){
                    if( fwd ){
                        pair[0] = 0;
                    }
                    else {
                        pair[0] = $headerCellsNoPersist.length - 1;
                    }
                }

                if( supportsNthChild ) {
                    maintainWidths();
                }

                hideColumn( $headerCellsNoPersist.get( pair[ 0 ] ) );
                showColumn( $headerCellsNoPersist.get( pair[ 1 ] ) );

                $table.trigger( 'tablesawcolumns' );
            }
        }

        $prevBtn.add( $nextBtn ).click(function( e ){
            advance( !!$( e.target ).closest( $nextBtn ).length );
            e.preventDefault();
        });

        function getCoord( event, key ) {
            return ( event.touches || event.originalEvent.touches )[ 0 ][ key ];
        }

        $table
            .bind( "touchstart.swipetoggle", function( e ){
                var originX = getCoord( e, 'pageX' ),
                    originY = getCoord( e, 'pageY' ),
                    x,
                    y;

                $( win ).off( "resize", fakeBreakpoints );

                $( this )
                    .bind( "touchmove", function( e ){
                        x = getCoord( e, 'pageX' );
                        y = getCoord( e, 'pageY' );
                        var cfg = Tablesaw.config.swipe;
                        if( Math.abs( x - originX ) > cfg.horizontalThreshold && Math.abs( y - originY ) < cfg.verticalThreshold ) {
                            e.preventDefault();
                        }
                    })
                    .bind( "touchend.swipetoggle", function(){
                        var cfg = Tablesaw.config.swipe;
                        if( Math.abs( y - originY ) < cfg.verticalThreshold ) {
                            if( x - originX < -1 * cfg.horizontalThreshold ){
                                advance( true );
                            }
                            if( x - originX > cfg.horizontalThreshold ){
                                advance( false );
                            }
                        }

                        window.setTimeout(function() {
                            $( win ).on( "resize", fakeBreakpoints );
                        }, 300);
                        $( this ).unbind( "touchmove touchend" );
                    });

            })
            .bind( "tablesawcolumns.swipetoggle", function(){
                $prevBtn[ canAdvance( getPrev() ) ? "removeClass" : "addClass" ]( hideBtn );
                $nextBtn[ canAdvance( getNext() ) ? "removeClass" : "addClass" ]( hideBtn );
            })
            .bind( "tablesawnext.swipetoggle", function(){
                advance( true );
            } )
            .bind( "tablesawprev.swipetoggle", function(){
                advance( false );
            } )
            .bind( "tablesawdestroy.swipetoggle", function(){
                var $t = $( this );

                $t.removeClass( 'tablesaw-swipe' );
                $t.prev().filter( '.tablesaw-bar' ).find( '.tablesaw-advance' ).remove();
                $( win ).off( "resize", fakeBreakpoints );

                $t.unbind( ".swipetoggle" );
            });

        fakeBreakpoints();
        $( win ).on( "resize", fakeBreakpoints );
    }



    // on tablecreate, init
    $( document ).on( "tablesawcreate", function( e, Tablesaw ){

        if( Tablesaw.mode === 'swipe' ){
            createSwipeTable( Tablesaw.$table );
        }

    } );

}( this, jQuery ));

;(function( $ ) {
    function getSortValue( cell ) {
        return $.map( cell.childNodes, function( el ) {
            var $el = $( el );
            if( $el.is( 'input, select' ) ) {
                return $el.val();
            } else if( $el.hasClass( 'tablesaw-cell-label' ) ) {
                return;
            }
            return $.trim( $el.text() );
        }).join( '' );
    }

    var pluginName = "tablesaw-sortable",
        initSelector = "table[data-" + pluginName + "]",
        sortableSwitchSelector = "[data-" + pluginName + "-switch]",
        attrs = {
            defaultCol: "data-tablesaw-sortable-default-col",
            numericCol: "data-tablesaw-sortable-numeric"
        },
        classes = {
            head: pluginName + "-head",
            ascend: pluginName + "-ascending",
            descend: pluginName + "-descending",
            switcher: pluginName + "-switch",
            tableToolbar: 'tablesaw-toolbar',
            sortButton: pluginName + "-btn"
        },
        methods = {
            _create: function( o ){
                return $( this ).each(function() {
                    var init = $( this ).data( "init" + pluginName );
                    if( init ) {
                        return false;
                    }
                    $( this )
                        .data( "init"+ pluginName, true )
                        .trigger( "beforecreate." + pluginName )
                        [ pluginName ]( "_init" , o )
                        .trigger( "create." + pluginName );
                });
            },
            _init: function(){
                var el = $( this ),
                    heads,
                    $switcher;

                var addClassToTable = function(){
                        el.addClass( pluginName );
                    },
                    addClassToHeads = function( h ){
                        $.each( h , function( i , v ){
                            $( v ).addClass( classes.head );
                        });
                    },
                    makeHeadsActionable = function( h , fn ){
                        $.each( h , function( i , v ){
                            var b = $( "<button class='" + classes.sortButton + "'/>" );
                            b.bind( "click" , { col: v } , fn );
                            $( v ).wrapInner( b );
                        });
                    },
                    clearOthers = function( sibs ){
                        $.each( sibs , function( i , v ){
                            var col = $( v );
                            col.removeAttr( attrs.defaultCol );
                            col.removeClass( classes.ascend );
                            col.removeClass( classes.descend );
                        });
                    },
                    headsOnAction = function( e ){
                        if( $( e.target ).is( 'a[href]' ) ) {
                            return;
                        }

                        e.stopPropagation();
                        var head = $( this ).parent(),
                            v = e.data.col,
                            newSortValue = heads.index( head );

                        clearOthers( head.siblings() );
                        if( head.hasClass( classes.descend ) ){
                            el[ pluginName ]( "sortBy" , v , true);
                            newSortValue += '_asc';
                        } else {
                            el[ pluginName ]( "sortBy" , v );
                            newSortValue += '_desc';
                        }
                        if( $switcher ) {
                            $switcher.find( 'select' ).val( newSortValue ).trigger( 'refresh' );
                        }

                        e.preventDefault();
                    },
                    handleDefault = function( heads ){
                        $.each( heads , function( idx , el ){
                            var $el = $( el );
                            if( $el.is( "[" + attrs.defaultCol + "]" ) ){
                                if( !$el.hasClass( classes.descend ) ) {
                                    $el.addClass( classes.ascend );
                                }
                            }
                        });
                    },
                    addSwitcher = function( heads ){
                        $switcher = $( '<div>' ).addClass( classes.switcher ).addClass( classes.tableToolbar ).html(function() {
                            var html = [ '<label>' + Tablesaw.i18n.sort + ':' ];

                            html.push( '<span class="btn btn-small">&#160;<select>' );
                            heads.each(function( j ) {
                                var $t = $( this );
                                var isDefaultCol = $t.is( "[" + attrs.defaultCol + "]" );
                                var isDescending = $t.hasClass( classes.descend );

                                var hasNumericAttribute = $t.is( '[data-sortable-numeric]' );
                                var numericCount = 0;
                                // Check only the first four rows to see if the column is numbers.
                                var numericCountMax = 5;

                                $( this.cells ).slice( 0, numericCountMax ).each(function() {
                                    if( !isNaN( parseInt( getSortValue( this ), 10 ) ) ) {
                                        numericCount++;
                                    }
                                });
                                var isNumeric = numericCount === numericCountMax;
                                if( !hasNumericAttribute ) {
                                    $t.attr( "data-sortable-numeric", isNumeric ? "" : "false" );
                                }

                                html.push( '<option' + ( isDefaultCol && !isDescending ? ' selected' : '' ) + ' value="' + j + '_asc">' + $t.text() + ' ' + ( isNumeric ? '&#x2191;' : '(A-Z)' ) + '</option>' );
                                html.push( '<option' + ( isDefaultCol && isDescending ? ' selected' : '' ) + ' value="' + j + '_desc">' + $t.text() + ' ' + ( isNumeric ? '&#x2193;' : '(Z-A)' ) + '</option>' );
                            });
                            html.push( '</select></span></label>' );

                            return html.join('');
                        });

                        var $toolbar = el.prev().filter( '.tablesaw-bar' ),
                            $firstChild = $toolbar.children().eq( 0 );

                        if( $firstChild.length ) {
                            $switcher.insertBefore( $firstChild );
                        } else {
                            $switcher.appendTo( $toolbar );
                        }
                        $switcher.find( '.btn' ).tablesawbtn();
                        $switcher.find( 'select' ).on( 'change', function() {
                            var val = $( this ).val().split( '_' ),
                                head = heads.eq( val[ 0 ] );

                            clearOthers( head.siblings() );
                            el[ pluginName ]( 'sortBy', head.get( 0 ), val[ 1 ] === 'asc' );
                        });
                    };

                addClassToTable();
                heads = el.find( "thead th[data-" + pluginName + "-col]" );
                addClassToHeads( heads );
                makeHeadsActionable( heads , headsOnAction );
                handleDefault( heads );

                if( el.is( sortableSwitchSelector ) ) {
                    addSwitcher( heads, el.find('tbody tr:nth-child(-n+3)') );
                }
            },
            getColumnNumber: function( col ){
                return $( col ).prevAll().length;
            },
            getTableRows: function(){
                return $( this ).find( "tbody tr" );
            },
            sortRows: function( rows , colNum , ascending, col ){
                var cells, fn, sorted;
                var getCells = function( rows ){
                        var cells = [];
                        $.each( rows , function( i , r ){
                            var element = $( r ).children().get( colNum );
                            cells.push({
                                element: element,
                                cell: getSortValue( element ),
                                rowNum: i
                            });
                        });
                        return cells;
                    },
                    getSortFxn = function( ascending, forceNumeric ){
                        var fn,
                            regex = /[^\-\+\d\.]/g;
                        if( ascending ){
                            fn = function( a , b ){
                                if( forceNumeric ) {
                                    return parseFloat( a.cell.replace( regex, '' ) ) - parseFloat( b.cell.replace( regex, '' ) );
                                } else {
                                    return a.cell.toLowerCase() > b.cell.toLowerCase() ? 1 : -1;
                                }
                            };
                        } else {
                            fn = function( a , b ){
                                if( forceNumeric ) {
                                    return parseFloat( b.cell.replace( regex, '' ) ) - parseFloat( a.cell.replace( regex, '' ) );
                                } else {
                                    return a.cell.toLowerCase() < b.cell.toLowerCase() ? 1 : -1;
                                }
                            };
                        }
                        return fn;
                    },
                    applyToRows = function( sorted , rows ){
                        var newRows = [], i, l, cur;
                        for( i = 0, l = sorted.length ; i < l ; i++ ){
                            cur = sorted[ i ].rowNum;
                            newRows.push( rows[cur] );
                        }
                        return newRows;
                    };

                cells = getCells( rows );
                var customFn = $( col ).data( 'tablesaw-sort' );
                fn = ( customFn && typeof customFn === "function" ? customFn( ascending ) : false ) ||
                    getSortFxn( ascending, $( col ).is( '[data-sortable-numeric]' ) && !$( col ).is( '[data-sortable-numeric="false"]' ) );
                sorted = cells.sort( fn );
                rows = applyToRows( sorted , rows );
                return rows;
            },
            replaceTableRows: function( rows ){
                var el = $( this ),
                    body = el.find( "tbody" );
                body.html( rows );
            },
            makeColDefault: function( col , a ){
                var c = $( col );
                c.attr( attrs.defaultCol , "true" );
                if( a ){
                    c.removeClass( classes.descend );
                    c.addClass( classes.ascend );
                } else {
                    c.removeClass( classes.ascend );
                    c.addClass( classes.descend );
                }
            },
            sortBy: function( col , ascending ){
                var el = $( this ), colNum, rows;

                colNum = el[ pluginName ]( "getColumnNumber" , col );
                rows = el[ pluginName ]( "getTableRows" );
                rows = el[ pluginName ]( "sortRows" , rows , colNum , ascending, col );
                el[ pluginName ]( "replaceTableRows" , rows );
                el[ pluginName ]( "makeColDefault" , col , ascending );
            }
        };

    // Collection method.
    $.fn[ pluginName ] = function( arrg ) {
        var args = Array.prototype.slice.call( arguments , 1),
            returnVal;

        // if it's a method
        if( arrg && typeof( arrg ) === "string" ){
            returnVal = $.fn[ pluginName ].prototype[ arrg ].apply( this[0], args );
            return (typeof returnVal !== "undefined")? returnVal:$(this);
        }
        // check init
        if( !$( this ).data( pluginName + "data" ) ){
            $( this ).data( pluginName + "active", true );
            $.fn[ pluginName ].prototype._create.call( this , arrg );
        }
        return $(this);
    };
    // add methods
    $.extend( $.fn[ pluginName ].prototype, methods );

    $( document ).on( "tablesawcreate", function( e, Tablesaw ) {
        if( Tablesaw.$table.is( initSelector ) ) {
            Tablesaw.$table[ pluginName ]();
        }
    });

}( jQuery ));

;(function( win, $, undefined ){

    var MM = {
        attr: {
            init: 'data-tablesaw-minimap'
        }
    };

    function createMiniMap( $table ){

        var $btns = $( '<div class="tablesaw-advance minimap">' ),
            $dotNav = $( '<ul class="tablesaw-advance-dots">' ).appendTo( $btns ),
            hideDot = 'tablesaw-advance-dots-hide',
            $headerCells = $table.find( 'thead th' );

        // populate dots
        $headerCells.each(function(){
            $dotNav.append( '<li><i></i></li>' );
        });

        $btns.appendTo( $table.prev().filter( '.tablesaw-bar' ) );

        function showMinimap( $table ) {
            var mq = $table.attr( MM.attr.init );
            return !mq || win.matchMedia && win.matchMedia( mq ).matches;
        }

        function showHideNav(){
            if( !showMinimap( $table ) ) {
                $btns.hide();
                return;
            }
            $btns.show();

            // show/hide dots
            var dots = $dotNav.find( "li" ).removeClass( hideDot );
            $table.find( "thead th" ).each(function(i){
                if( $( this ).css( "display" ) === "none" ){
                    dots.eq( i ).addClass( hideDot );
                }
            });
        }

        // run on init and resize
        showHideNav();
        $( win ).on( "resize", showHideNav );


        $table
            .bind( "tablesawcolumns.minimap", function(){
                showHideNav();
            })
            .bind( "tablesawdestroy.minimap", function(){
                var $t = $( this );

                $t.prev().filter( '.tablesaw-bar' ).find( '.tablesaw-advance' ).remove();
                $( win ).off( "resize", showHideNav );

                $t.unbind( ".minimap" );
            });
    }



    // on tablecreate, init
    $( document ).on( "tablesawcreate", function( e, Tablesaw ){

        if( ( Tablesaw.mode === 'swipe' || Tablesaw.mode === 'columntoggle' ) && Tablesaw.$table.is( '[ ' + MM.attr.init + ']' ) ){
            createMiniMap( Tablesaw.$table );
        }

    } );

}( this, jQuery ));

;(function( win, $ ) {

    var S = {
        selectors: {
            init: 'table[data-tablesaw-mode-switch]'
        },
        attributes: {
            excludeMode: 'data-tablesaw-mode-exclude'
        },
        classes: {
            main: 'tablesaw-modeswitch',
            toolbar: 'tablesaw-toolbar'
        },
        modes: [ 'stack', 'swipe', 'columntoggle' ],
        init: function( table ) {
            var $table = $( table ),
                ignoreMode = $table.attr( S.attributes.excludeMode ),
                $toolbar = $table.prev().filter( '.tablesaw-bar' ),
                modeVal = '',
                $switcher = $( '<div>' ).addClass( S.classes.main + ' ' + S.classes.toolbar ).html(function() {
                    var html = [ '<label>' + Tablesaw.i18n.columns + ':' ],
                        dataMode = $table.attr( 'data-tablesaw-mode' ),
                        isSelected;

                    html.push( '<span class="btn btn-small">&#160;<select>' );
                    for( var j=0, k = S.modes.length; j<k; j++ ) {
                        if( ignoreMode && ignoreMode.toLowerCase() === S.modes[ j ] ) {
                            continue;
                        }

                        isSelected = dataMode === S.modes[ j ];

                        if( isSelected ) {
                            modeVal = S.modes[ j ];
                        }

                        html.push( '<option' +
                            ( isSelected ? ' selected' : '' ) +
                            ' value="' + S.modes[ j ] + '">' + Tablesaw.i18n.modes[ j ] + '</option>' );
                    }
                    html.push( '</select></span></label>' );

                    return html.join('');
                });

            var $otherToolbarItems = $toolbar.find( '.tablesaw-advance' ).eq( 0 );
            if( $otherToolbarItems.length ) {
                $switcher.insertBefore( $otherToolbarItems );
            } else {
                $switcher.appendTo( $toolbar );
            }

            $switcher.find( '.btn' ).tablesawbtn();
            $switcher.find( 'select' ).bind( 'change', S.onModeChange );
        },
        onModeChange: function() {
            var $t = $( this ),
                $switcher = $t.closest( '.' + S.classes.main ),
                $table = $t.closest( '.tablesaw-bar' ).nextUntil( $table ).eq( 0 ),
                val = $t.val();

            $switcher.remove();
            $table.data( 'table' ).destroy();

            $table.attr( 'data-tablesaw-mode', val );
            $table.table();
        }
    };

    $( win.document ).on( "tablesawcreate", function( e, Tablesaw ) {
        if( Tablesaw.$table.is( S.selectors.init ) ) {
            S.init( Tablesaw.table );
        }
    });

})( this, jQuery );
/*tablesaw-init.js*/
/*! Tablesaw - v2.0.2 - 2015-10-28
* https://github.com/filamentgroup/tablesaw
* Copyright (c) 2015 Filament Group; Licensed  */
;(function( $ ) {

    // DOM-ready auto-init of plugins.
    // Many plugins bind to an "enhance" event to init themselves on dom ready, or when new markup is inserted into the DOM
    $( function(){
        $( document ).trigger( "enhance.tablesaw" );
    });

})( jQuery );
/*imagesloaded.js*/
/*!
 * imagesLoaded v3.0.2
 * JavaScript is all like "You images are done yet or what?"
 */

( function( window ) {

    'use strict';

    var $ = window.jQuery;
    var console = window.console;
    var hasConsole = typeof console !== 'undefined';

// -------------------------- helpers -------------------------- //

// extend objects
    function extend( a, b ) {
        for ( var prop in b ) {
            a[ prop ] = b[ prop ];
        }
        return a;
    }

    var objToString = Object.prototype.toString;
    function isArray( obj ) {
        return objToString.call( obj ) === '[object Array]';
    }

// turn element or nodeList into an array
    function makeArray( obj ) {
        var ary = [];
        if ( isArray( obj ) ) {
            // use object if already an array
            ary = obj;
        } else if ( typeof obj.length === 'number' ) {
            // convert nodeList to array
            for ( var i=0, len = obj.length; i < len; i++ ) {
                ary.push( obj[i] );
            }
        } else {
            // array of single index
            ary.push( obj );
        }
        return ary;
    }

// --------------------------  -------------------------- //

    function defineImagesLoaded( EventEmitter, eventie ) {

        /**
         * @param {Array, Element, NodeList, String} elem
         * @param {Object or Function} options - if function, use as callback
         * @param {Function} onAlways - callback function
         */
        function ImagesLoaded( elem, options, onAlways ) {
            // coerce ImagesLoaded() without new, to be new ImagesLoaded()
            if ( !( this instanceof ImagesLoaded ) ) {
                return new ImagesLoaded( elem, options );
            }
            // use elem as selector string
            if ( typeof elem === 'string' ) {
                elem = document.querySelectorAll( elem );
            }

            this.elements = makeArray( elem );
            this.options = extend( {}, this.options );

            if ( typeof options === 'function' ) {
                onAlways = options;
            } else {
                extend( this.options, options );
            }

            if ( onAlways ) {
                this.on( 'always', onAlways );
            }

            this.getImages();

            if ( $ ) {
                // add jQuery Deferred object
                this.jqDeferred = new $.Deferred();
            }

            // HACK check async to allow time to bind listeners
            var _this = this;
            setTimeout( function() {
                _this.check();
            });
        }

        ImagesLoaded.prototype = new EventEmitter();

        ImagesLoaded.prototype.options = {};

        ImagesLoaded.prototype.getImages = function() {
            this.images = [];

            // filter & find items if we have an item selector
            for ( var i=0, len = this.elements.length; i < len; i++ ) {
                var elem = this.elements[i];
                // filter siblings
                if ( elem.nodeName === 'IMG' ) {
                    this.addImage( elem );
                }
                // find children
                var childElems = elem.querySelectorAll('img');
                // concat childElems to filterFound array
                for ( var j=0, jLen = childElems.length; j < jLen; j++ ) {
                    var img = childElems[j];
                    this.addImage( img );
                }
            }
        };

        /**
         * @param {Image} img
         */
        ImagesLoaded.prototype.addImage = function( img ) {
            var loadingImage = new LoadingImage( img );
            this.images.push( loadingImage );
        };

        ImagesLoaded.prototype.check = function() {
            var _this = this;
            var checkedCount = 0;
            var length = this.images.length;
            this.hasAnyBroken = false;
            // complete if no images
            if ( !length ) {
                this.complete();
                return;
            }

            function onConfirm( image, message ) {
                if ( _this.options.debug && hasConsole ) {
                    console.log( 'confirm', image, message );
                }

                _this.progress( image );
                checkedCount++;
                if ( checkedCount === length ) {
                    _this.complete();
                }
                return true; // bind once
            }

            for ( var i=0; i < length; i++ ) {
                var loadingImage = this.images[i];
                loadingImage.on( 'confirm', onConfirm );
                loadingImage.check();
            }
        };

        ImagesLoaded.prototype.progress = function( image ) {
            this.hasAnyBroken = this.hasAnyBroken || !image.isLoaded;
            this.emit( 'progress', this, image );
            if ( this.jqDeferred ) {
                this.jqDeferred.notify( this, image );
            }
        };

        ImagesLoaded.prototype.complete = function() {
            var eventName = this.hasAnyBroken ? 'fail' : 'done';
            this.isComplete = true;
            this.emit( eventName, this );
            this.emit( 'always', this );
            if ( this.jqDeferred ) {
                var jqMethod = this.hasAnyBroken ? 'reject' : 'resolve';
                this.jqDeferred[ jqMethod ]( this );
            }
        };

        // -------------------------- jquery -------------------------- //

        if ( $ ) {
            $.fn.imagesLoaded = function( options, callback ) {
                var instance = new ImagesLoaded( this, options, callback );
                return instance.jqDeferred.promise( $(this) );
            };
        }


        // --------------------------  -------------------------- //

        var cache = {};

        function LoadingImage( img ) {
            this.img = img;
        }

        LoadingImage.prototype = new EventEmitter();

        LoadingImage.prototype.check = function() {
            // first check cached any previous images that have same src
            var cached = cache[ this.img.src ];
            if ( cached ) {
                this.useCached( cached );
                return;
            }
            // add this to cache
            cache[ this.img.src ] = this;

            // If complete is true and browser supports natural sizes,
            // try to check for image status manually.
            if ( this.img.complete && this.img.naturalWidth !== undefined ) {
                // report based on naturalWidth
                this.confirm( this.img.naturalWidth !== 0, 'naturalWidth' );
                return;
            }

            // If none of the checks above matched, simulate loading on detached element.
            var proxyImage = this.proxyImage = new Image();
            eventie.bind( proxyImage, 'load', this );
            eventie.bind( proxyImage, 'error', this );
            proxyImage.src = this.img.src;
        };

        LoadingImage.prototype.useCached = function( cached ) {
            if ( cached.isConfirmed ) {
                this.confirm( cached.isLoaded, 'cached was confirmed' );
            } else {
                var _this = this;
                cached.on( 'confirm', function( image ) {
                    _this.confirm( image.isLoaded, 'cache emitted confirmed' );
                    return true; // bind once
                });
            }
        };

        LoadingImage.prototype.confirm = function( isLoaded, message ) {
            this.isConfirmed = true;
            this.isLoaded = isLoaded;
            this.emit( 'confirm', this, message );
        };

        // trigger specified handler for event type
        LoadingImage.prototype.handleEvent = function( event ) {
            var method = 'on' + event.type;
            if ( this[ method ] ) {
                this[ method ]( event );
            }
        };

        LoadingImage.prototype.onload = function() {
            this.confirm( true, 'onload' );
            this.unbindProxyEvents();
        };

        LoadingImage.prototype.onerror = function() {
            this.confirm( false, 'onerror' );
            this.unbindProxyEvents();
        };

        LoadingImage.prototype.unbindProxyEvents = function() {
            eventie.unbind( this.proxyImage, 'load', this );
            eventie.unbind( this.proxyImage, 'error', this );
        };

        // -----  ----- //

        return ImagesLoaded;
    }

// -------------------------- transport -------------------------- //

    if ( typeof define === 'function' && define.amd ) {
        // AMD
        define( [
                'eventEmitter',
                'eventie'
            ],
            defineImagesLoaded );
    } else {
        // browser global
        window.imagesLoaded = defineImagesLoaded(
            window.EventEmitter,
            window.eventie
        );
    }

})( window );
/*classie.js*/
/*!
 * classie - class helper functions
 * from bonzo https://github.com/ded/bonzo
 *
 * classie.has( elem, 'my-class' ) -> true/false
 * classie.add( elem, 'my-new-class' )
 * classie.remove( elem, 'my-unwanted-class' )
 * classie.toggle( elem, 'my-class' )
 */

/*jshint browser: true, strict: true, undef: true */
/*global define: false */

( function( window ) {

    'use strict';

// class helper functions from bonzo https://github.com/ded/bonzo

    function classReg( className ) {
        return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
    }

// classList support for class management
// altho to be fair, the api sucks because it won't accept multiple classes at once
    var hasClass, addClass, removeClass;

    if ( 'classList' in document.documentElement ) {
        hasClass = function( elem, c ) {
            return elem.classList.contains( c );
        };
        addClass = function( elem, c ) {
            elem.classList.add( c );
        };
        removeClass = function( elem, c ) {
            elem.classList.remove( c );
        };
    }
    else {
        hasClass = function( elem, c ) {
            return classReg( c ).test( elem.className );
        };
        addClass = function( elem, c ) {
            if ( !hasClass( elem, c ) ) {
                elem.className = elem.className + ' ' + c;
            }
        };
        removeClass = function( elem, c ) {
            elem.className = elem.className.replace( classReg( c ), ' ' );
        };
    }

    function toggleClass( elem, c ) {
        var fn = hasClass( elem, c ) ? removeClass : addClass;
        fn( elem, c );
    }

    var classie = {
        // full names
        hasClass: hasClass,
        addClass: addClass,
        removeClass: removeClass,
        toggleClass: toggleClass,
        // short names
        has: hasClass,
        add: addClass,
        remove: removeClass,
        toggle: toggleClass
    };

// transport
    if ( typeof define === 'function' && define.amd ) {
        // AMD
        define( classie );
    } else {
        // browser global
        window.classie = classie;
    }

})( window );

/*AnimOnScroll.js*/
/**
 * animOnScroll.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */
;( function( window ) {

    'use strict';

    var docElem = window.document.documentElement;

    function getViewportH() {
        var client = docElem['clientHeight'],
            inner = window['innerHeight'];

        if( client < inner )
            return inner;
        else
            return client;
    }

    function scrollY() {
        return window.pageYOffset || docElem.scrollTop;
    }

    // http://stackoverflow.com/a/5598797/989439
    function getOffset( el ) {
        var offsetTop = 0, offsetLeft = 0;
        do {
            if ( !isNaN( el.offsetTop ) ) {
                offsetTop += el.offsetTop;
            }
            if ( !isNaN( el.offsetLeft ) ) {
                offsetLeft += el.offsetLeft;
            }
        } while( el = el.offsetParent )

        return {
            top : offsetTop,
            left : offsetLeft
        }
    }

    function inViewport( el, h ) {
        var elH = el.offsetHeight,
            scrolled = scrollY(),
            viewed = scrolled + getViewportH(),
            elTop = getOffset(el).top,
            elBottom = elTop + elH,
            // if 0, the element is considered in the viewport as soon as it enters.
            // if 1, the element is considered in the viewport only when it's fully inside
            // value in percentage (1 >= h >= 0)
            h = h || 0;

        return (elTop + elH * h) <= viewed && (elBottom - elH * h) >= scrolled;
    }

    function extend( a, b ) {
        for( var key in b ) {
            if( b.hasOwnProperty( key ) ) {
                a[key] = b[key];
            }
        }
        return a;
    }

    function AnimOnScroll( el, options ) {
        this.el = el;
        this.options = extend( this.defaults, options );
        this._init();
    }

    AnimOnScroll.prototype = {
        defaults : {
            // Minimum and a maximum duration of the animation (random value is chosen)
            minDuration : 0,
            maxDuration : 0,
            // The viewportFactor defines how much of the appearing item has to be visible in order to trigger the animation
            // if we'd use a value of 0, this would mean that it would add the animation class as soon as the item is in the viewport.
            // If we were to use the value of 1, the animation would only be triggered when we see all of the item in the viewport (100% of it)
            viewportFactor : 0,
            MasonryOk 	: true
        },
        _init : function() {
            this.items = Array.prototype.slice.call( document.querySelectorAll( '#' + this.el.id + ' > li' ) );
            this.itemsCount = this.items.length;
            this.itemsRenderedCount = 0;
            this.didScroll = false;

            var self = this;

            imagesLoaded( this.el, function() {

                // initialize masonry
                var msnry = new Masonry( self.el, {
                    itemSelector: 'li',
                    transitionDuration : 0
                } );
                window.metgrid = msnry;

                if( Modernizr.cssanimations ) {
                    // the items already shown...
                    self.items.forEach( function( el, i ) {
                        if( inViewport( el ) ) {
                            self._checkTotalRendered();
                            classie.add( el, 'shown' );
                        }
                    } );

                    // animate on scroll the items inside the viewport
                    window.addEventListener( 'scroll', function() {
                        self._onScrollFn();
                    }, false );
                    window.addEventListener( 'resize', function() {
                        self._resizeHandler();
                    }, false );
                }

            });
        },
        _onScrollFn : function() {
            var self = this;
            if( !this.didScroll ) {
                this.didScroll = true;
                setTimeout( function() { self._scrollPage(); }, 60 );
            }
        },
        _scrollPage : function() {
            var self = this;
            this.items.forEach( function( el, i ) {
                if( !classie.has( el, 'shown' ) && !classie.has( el, 'animate' ) && inViewport( el, self.options.viewportFactor ) ) {
                    setTimeout( function() {
                        var perspY = scrollY() + getViewportH() / 2;
                        self.el.style.WebkitPerspectiveOrigin = '50% ' + perspY + 'px';
                        self.el.style.MozPerspectiveOrigin = '50% ' + perspY + 'px';
                        self.el.style.perspectiveOrigin = '50% ' + perspY + 'px';

                        self._checkTotalRendered();

                        if( self.options.minDuration && self.options.maxDuration ) {
                            var randDuration = ( Math.random() * ( self.options.maxDuration - self.options.minDuration ) + self.options.minDuration ) + 's';
                            el.style.WebkitAnimationDuration = randDuration;
                            el.style.MozAnimationDuration = randDuration;
                            el.style.animationDuration = randDuration;
                        }

                        classie.add( el, 'animate' );
                    }, 25 );
                }
            });
            this.didScroll = false;
        },
        _resizeHandler : function() {
            var self = this;
            function delayed() {
                self._scrollPage();
                self.resizeTimeout = null;
            }
            if ( this.resizeTimeout ) {
                clearTimeout( this.resizeTimeout );
            }
            this.resizeTimeout = setTimeout( delayed, 1000 );
        },
        _checkTotalRendered : function() {
            ++this.itemsRenderedCount;
            if( this.itemsRenderedCount === this.itemsCount ) {
                window.removeEventListener( 'scroll', this._onScrollFn );
            }
        }
    }

    // add to global namespace
    window.AnimOnScroll = AnimOnScroll;

} )( window );
