var M=new Array();
M['classnow']=10001;

$(document).ready(function(e) {

    /*底部微信*/
    if($('#met-weixin').length){
        var defaults = $.components.getDefaults("webuiPopover");
        var weixinContent = $('#met-weixin-content').html(),
            weixinSettings = {
                title: '',
                content: weixinContent,
                placement:'top',
                trigger:'hover',
                width: 132,
                padding:false,
                offsetTop:-10,
                animation:'pop'
            };
        $('#met-weixin').webuiPopover($.extend({}, defaults, weixinSettings));
    }

    nfun($('.nav-cut li.nav1.active'),$('.nav-cut li.nav1.active'));

    $('.nav-cut li.nav1').mouseover(function(){
        if($(window).width()>=768){
            that=$(this);
            nfun(that,that);
            that.find('>ul').css('top',that.offset().top-$(window).scrollTop()+36/2);
        }
    }).mouseout(function(){
        if($(window).width()>=768){
            nfun($(this),$('.nav-cut>ul>li.nav1.active'));
        }
    }).click(function(){
        if($(window).width()<768){
            that=$(this);
            nfun(that,that);
            that.find('>ul').css('top',that.offset().top-$(window).scrollTop()+36/2);
        }
    });
    function nfun(that,the){
        $('.nav-cut a').removeAttr('style');
        the.children('a').css('color','#fff');
        that.parent('ul').find('.background').css({'top':the.index()*36});
    }

    $(window).resize(function(){
        if($(window).width()>=768){
            $('.nav-cut ul').show();
        }
    });
    $('.nav-cut>ul>li>a').click(function(){
        if($(window).width()<768){
            if($(this).next('ul').css('display')=='none'){
                $('.nav-cut>ul>li>ul').hide();
                $(this).next('ul').show();
            }else{
                $(this).next('ul').hide();
            }
        }
    });
    $('.nav-cut>ul>li>ul>li>a').click(function(){
        if($(window).width()<768){
            if($(this).next('ul').css('display')=='none'){
                $(this).next('ul').show();
            }else{
                $(this).next('ul').hide();
            }
        }
    });


    $('.sidebar-search i.fa-search').click(function(){
        $(this).parent('form').submit();
    });


    navc();
    $(window).resize(function(){ navc(); });
    function navc(){
        if($(window).width()<768){
            $.cookie('head_active','current',{path:'/'});
        }
        if($(window).width()<1200){
            if(!$('.nav-cut li.one').length>0){
                $('.nav-cut a').each(function(){
                    if($(this).next('ul').length>0){
                        $(this).next('ul').prepend(
                            '<li class="one">'+
                            '<a href="'+$(this).attr('href')+'" title="'+$(this).attr('title')+'">'+
                            $(this).attr('data-alert')+
                            '</a>'+
                            '</li>');
                        $(this).attr('data-href',$(this).attr('href'));
                        $(this).removeAttr('href');
                    }
                });
            }
        }else{
            $('.nav-cut li.one').remove();
            $('.nav-cut a[data-href]').each(function(){
                $(this).attr('href',$(this).attr('data-href'));
                $(this).removeAttr('data-href');
            });
        }
        $('.nav-cut').removeAttr('style');
        win_width=$(window).width();
        win_height=$(window).height();
        nav_heigth=win_height-$('.head-top').height()-$('.head-bottom').height();
        $('.nav-cut').css('min-height',nav_heigth);
        $('.head-box').removeAttr('style');
        if(nav_heigth>=144 && win_width>=768){
            $('.nav-cut').height(nav_heigth);
            $('.head-box').css('overflow','visible');
        }
    }


    var IE9=(navigator.userAgent.indexOf("MSIE 9.0")>0)?true:false,title=new Array(),hash=new Array();
    if(M['classnow']==10001){

        $('.head-rights ol,.worth-text a').click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('section[role=main],header[role=heading]').removeClass('active');
            }else{
                $(this).addClass('active');
                $('section[role=main],header[role=heading]').addClass('active');
            }
        });
        wfun($('.window-head ul li.active'),1);
        $(window).resize(function(){
            that=$('.window-head ul li.active');
            if(that.length>0){
                wleft=that.offset().left+15;
                wwidth=that.width()+10;
                $('.window-head hr').css({'left':wleft,'width':wwidth});
            }
        });
        $('.window-head ul').on('mouseover','li',function(){
            wfun($(this),1);
        }).on('mouseout','li',function(){
            wfun($('.window-head ul li.active'),2);
        });
        function wfun(that,times){
            if(typeof(wtime)!='undefined') clearTimeout(wtime);
            wtime=window.setTimeout(function(){
                if(that.length>0){
                    wleft=that.offset().left+15;
                    wwidth=that.width()+10;
                    $('.window-head hr').css({'left':wleft,'width':wwidth});
                }
            },times);
        }

        if($('.banner-box').length>0){
            banner_slide=new Swiper('.banner-box', {
                wrapperClass: 'banner-cut',
                slideClass: 'banner-bin',
                speed: 500,
                loop: true,
                autoplay: 4500,
                autoplayDisableOnInteraction: true,
                grabCursor: true,
                keyboardControl: true,
                slidesPerView: 1,
                pagination: '.banner-pager',
                paginationClickable :true
            });
            $('.banner-ctrl .ctrl-left').click(function(){
                banner_slide.slidePrev();
            });
            $('.banner-ctrl .ctrl-right').click(function(){
                banner_slide.slideNext();
            });
        }
        if($('.banner-span').length>0){
            banner_span=new Swiper('.banner-span',{
                autoplayDisableOnInteraction: true,
                wrapperClass: 'banner-ol',
                slideClass: 'banner-li',
                direction: 'vertical',
                loop: true,
                speed: 1000,
                autoplay: 2500,
                slidesPerView: 1,
                bulletActiveClass: 'active'
            });
        }

        if($('.case-box').length>0){
            case_number = $('.case-bin').length;
            case_slide = new Swiper('.case-box', {
                wrapperClass: 'case-cut',
                slideClass: 'case-bin',
                keyboardControl: true,
                width: 1260,
                loop: case_number>=4,
                autoplay: false,
                slidesPerView: 3,
                spaceBetween: 20,
                grabCursor: true,
                breakpoints: {
                    1599: { width:940, slidesPerView:3, loop:case_number>=3 },
                    1200: { width:620, slidesPerView:2, loop:case_number>=2 },
                    767: {  width:460, slidesPerView:2, loop:case_number>=3, spaceBetween: 30 },
                    479: {  width:300, slidesPerView:1, loop:case_number>=1 },
                    400: {  width:230, slidesPerView:1, loop:case_number>=1 }
                }
            });
            $('.case-ctrl .ctrl-left').click(function(){
                case_slide.slidePrev();
            });
            $('.case-ctrl .ctrl-right').click(function(){
                case_slide.slideNext();
            });
        }

        if($('.joint-box').length>0){
            if(IE9&&$('.joint-bin').length>5){
                joint_arr=[];
                joint_num=0;
                joint_htm='';
                $('.joint-bin').each(function(index,element){
                    joint_arr[index]=$(this).html();
                    joint_num++;
                });
                joint_len=Math.ceil(joint_num/3);
                for(i=0;i<joint_len;i++){
                    joint_htm+='<li class="joint-bin" style="width:230px;">'+joint_arr[i]+'</li>';
                }
                $('.joint-cut').html(joint_htm);
            }
            joint_slide = new Swiper('.joint-box', {
                wrapperClass: 'joint-cut',
                slideClass: 'joint-bin',
                slidesPerView: 5,
                slidesPerColumn: IE9?1:3,
                slidesPerColumnFill: 'row',
                speed: 500,
                width: 1150,
                autoplay: 4500,
                grabCursor: true,
                keyboardControl: true,
                breakpoints: {
                    1440: { width:1000, slidesPerView:4, slidesPerColumn:2 },
                    1200: { width:750, slidesPerView:3, slidesPerColumn:2 },
                    992: {  width:500, slidesPerView:2, slidesPerColumn:3 },
                    767: {  width:480, slidesPerView:3, slidesPerColumn:2 },
                    480: {  width:319, slidesPerView:2, slidesPerColumn:4 },
                    320: {  width:319, slidesPerView:2, slidesPerColumn:3 }
                }
            });
            if(IE9&&$('.joint-bin').length>5){
                joint_htm='';
                for(i=joint_len;i<joint_num;i++){
                    joint_htm+='<li class="joint-bin" style="width:230px;">'+joint_arr[i]+'</li>';
                }
                $('.joint-cut').append('<div>'+joint_htm+'</div>');
            }
            /**$('.joint-ctrl .ctrl-left').click(function(){
                joint_slide.slidePrev();
            });
            $('.joint-ctrl .ctrl-right').click(function(){
                joint_slide.slideNext();
            });**/
        }

        if($('.worth-box').length>0){
            worth_number = $('.worth-bin').length;
            worth_number = $('.worth-box video').length==0?worth_number:0;
            worth_slide = new Swiper('.worth-box', {
                wrapperClass: 'worth-cut',
                slideClass: 'worth-bin',
                keyboardControl: true,
                width: 1120,
                loop: worth_number>=4,
                autoplay: false,
                slidesPerView: 3,
                grabCursor: true,
                breakpoints: {
                    1200: { width:840, slidesPerView:3, loop:worth_number>=3 },
                    992: {  width:560, slidesPerView:2, loop:worth_number>=2 },
                    479: {  width:280, slidesPerView:1, loop:worth_number>=1 }
                }
            });
            /**$('.worth-ctrl .ctrl-left').click(function(){
                worth_slide.slidePrev();
            });
            $('.worth-ctrl .ctrl-right').click(function(){
                worth_slide.slideNext();
            });**/
        }

        if($('.mark-box').length>0){
            $('.mark-bin p').each(function(index, element) {
                if(index===0) hg=0;
                if($(this).height()>hg){
                    hg=$(this).height();
                    $('.mark-bin p').height(hg);
                }
            });
            mark_number = $('.mark-bin').length;
            mark_slide = new Swiper('.mark-box', {
                wrapperClass: 'mark-cut',
                slideClass: 'mark-bin',
                keyboardControl: true,
                width: 1200,
                loop: mark_number>=6,
                autoplay: false,
                slidesPerView: 6,
                slidesPerColumn: 1,
                grabCursor: true,
                breakpoints: {
                    1200: { width:900, slidesPerView:5, loop:mark_number>=5 },
                    992: {  width:600, slidesPerView:3, loop:mark_number>=3 },
                    767: {  width:320, slidesPerView:2, loop:mark_number>=2 },
                    479: {  width:320, slidesPerView:2, loop:mark_number>=2 }
                }
            });
            /**$('.mark-ctrl .ctrl-left').click(function(){
                mark_slide.slidePrev();
            });
            $('.mark-ctrl .ctrl-right').click(function(){
                mark_slide.slideNext();
            });**/
        }

        if($('.about-box').length>0){
            about_box=new Swiper('.about-box',{
                wrapperClass: 'about-cut',
                slideClass: 'about-bin',
                keyboardControl: true,
                autoplay: 3500,
                autoheight: true,
                speed: 500,
                spaceBetween: 10,
                slidesPerView: 1,
                pagination: '.about-nav ul',
                bulletClass: 'cut',
                bulletActiveClass: 'active',
                paginationClickable: true,
                paginationBulletRender: function(index,className){
                    title=$('.about-bin').eq(index).attr('title')||'';
                    return '<li class="'+className+'" data-index="'+(index+1)+'">'+title+'</li>';
                }
            });
            function about_height(){
                var about_back=$('.window-bin[data-hash=about] .window-back').height()-70;
                if( $('.about-bottom').css('display')=='block' ) about_back=about_back-$('.about-bottom').height();
                if( $('.about-nav').css('position')=='relative' ) about_back=about_back-$('.about-nav').height();
                about_back=about_back-$('.about-box').css('margin-top').replace('px','');
                $('.about-bin').css('max-height', about_back);
            }
            about_height();
            $(window).resize(function(){ about_height(); });
        }
        if($('.about-bottom').length>0){
            about_number = $('.about-li').length;
            about_slide = new Swiper('.about-bottom',{
                wrapperClass: 'about-ul',
                slideClass: 'about-li',
                keyboardControl: true,
                slidesPerView: 5,
                loop: about_number>=5,
                autoplay: 4500,
                grabCursor: true,
                breakpoints: {
                    1200: { slidesPerView:3, loop:about_number>=3 },
                    992:  { slidesPerView:2, loop:about_number>=2 },
                    767:  { slidesPerView:1, loop:about_number>=1 },
                    479:  { slidesPerView:1, loop:about_number>=1 }
                }
            });
        }

        if($('.window-box').length>0){
            if(IE9) $('.window-bin').height($('body').height());
            window_box=new Swiper('.window-box',{
                wrapperClass: 'window-cut',
                slideClass: 'window-bin',
                direction: 'vertical',
                lazyLoading: true,
                lazyLoadingOnTransitionStart: true,
                speed: 700,
                hashnav: true,
                bulletClass: 'cut',
                roundLengths: true,
                slidesPerView: 'auto',
                resistanceRatio: 0,
                keyboardControl: true,
                mousewheelControl: IE9?false:true,
                pagination: '.window-head ul',
                bulletActiveClass: 'active',
                paginationClickable: true,
                paginationBulletRender: function(index,className){
                    title=$('.window-bin').eq(index).find('.window-back').attr('data-title')||'';
                    if(title!='') return '<li class="'+className+'" data-index="'+(index+1)+'">'+title+'</li>';
                },
                onSlideChangeStart: function(swiper){
                    if(swiper.activeIndex==0){
                        $('.window-head').removeClass('active');
                    }else{
                        $('.window-head').addClass('active');
                    }
                    window.setTimeout(function(){
                        wfun($('.window-head ul li.active'),2);
                    },400);
                    if(typeof(banner_slide)!='undefined')  banner_slide.stopAutoplay();
                    if(typeof(banner_span)!='undefined')   banner_span.stopAutoplay();
                    if(typeof(case_slide)!='undefined')    case_slide.stopAutoplay();
                    if(typeof(joint_slide)!='undefined')   joint_slide.stopAutoplay();
                    if(typeof(worth_slide)!='undefined')   worth_slide.stopAutoplay();
                    if(typeof(mark_slide)!='undefined')    mark_slide.stopAutoplay();
                    if(typeof(about_box)!='undefined')     about_box.stopAutoplay();
                    if(typeof(about_slide)!='undefined')   about_slide.stopAutoplay();
                    switch( $('.window-bin').eq(swiper.activeIndex).attr('data-hash') ){
                        case 'banner':
                            if(typeof(banner_slide)!='undefined') banner_slide.startAutoplay();
                            if(typeof(banner_span)!='undefined') banner_span.startAutoplay();
                            break;
                        case 'icon':

                            break;
                        case 'case':
                            if(typeof(case_slide)!='undefined') case_slide.startAutoplay();
                            break;
                        case 'joint':
                            if(typeof(joint_slide)!='undefined') joint_slide.startAutoplay();
                            break;
                        case 'worth':
                            if(typeof(worth_slide)!='undefined') worth_slide.startAutoplay();
                            break;
                        case 'mark':
                            if(typeof(mark_slide)!='undefined') mark_slide.startAutoplay();
                            break;
                        case 'about':
                            if(typeof(about_box)!='undefined') about_box.startAutoplay();
                            if(typeof(about_slide)!='undefined') about_slide.startAutoplay();
                            break;
                        case 'contact':

                            break;
                    }
                }
            });
            if(IE9){
                window_time = '';
                window.onmousewheel=function(e){
                    clearTimeout(window_time);
                    window_time = window.setTimeout(function(){
                        if(e.wheelDelta>0){
                            window_box.slidePrev();
                        }else{
                            window_box.slideNext();
                        }
                    },500);
                }
            }
            if(typeof(window_box)!='undefined'){
                $('.banner-down').click(function(){
                    window_box.slideNext();
                });
                $('.head-left').click(function(){
                    window_box.slideTo(0);
                });
            }
        }

    }else{


        if($.cookie('head_active')=='current'){
            $('.sidebar-icon').removeClass('active');
            $('section[role=main],header[role=heading]').removeClass('active');
        }
        $('.sidebar-icon').click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('section[role=main],header[role=heading]').removeClass('active');
                $.cookie('head_active','current',{path:'/'});
            }else{
                $(this).addClass('active');
                $('section[role=main],header[role=heading]').addClass('active');
                $.cookie('head_active','active',{path:'/'});
            }
            window.setTimeout(function(){
                if($('#met-grid').length>0) MetAnimOnScroll();
            },500);
        });
        $('.case-bin.pro').width();
        $('.sidebar-nav>ul>li>a').click(function(e){
            if($(this).next('ul').length>0){
                if($(window).width()<1200){
                    e.preventDefault();
                }
            }
        });
        $(".swiper-lazy[data-original]").lazyload({
            threshold : 150,
            effect : "fadeIn"
        });


        bc=$('.banner-click');
        if(bc.length>0){
            dh=bc.next('.met-banner').attr('data-height').split('|');
            function proban(){
                if($(window).width()>992)
                    bc.parents('.banner-content').css('max-height',dh[0]+'px');
                else if($(window).width()>=768)
                    bc.parents('.banner-content').css('max-height',dh[1]+'px');
                else
                    bc.parents('.banner-content').css('max-height',dh[2]+'px');
            }
            proban();
            $(window).resize(function(){ proban(); });
            bc.click(function(){
                $('.banner-content').css('max-height','none');
                $(this).remove();
            });
        }


        $(".information-hots [data-original],.case-bin.pro [data-original],.news_img [data-original]").lazyload({
            threshold : 150,
            effect : "fadeIn"
        });
        $(".show-box [data-original],.information-content [data-original],.product-content [data-original]").lazyload({
            threshold: 150,
            effect: "fadeIn",
            load: function(){
                $(this).height('auto');
            }
        });
        if(IE9) $(".met-img .vertical-align-middle").css('margin-top',$(".met-img .overlay-fade").height()/2-18);
    }



    $('.met-column-nav li').click(function(){
        if($(window).width()/2<$(this).offset().left){
            $(this).find('.dropdown-menu').addClass('right');
        }else{
            $(this).find('.dropdown-menu').removeClass('right');
        }
    });
});
