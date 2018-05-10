$(document).ready(function () {
    _nav()
    $('header.active nav').on('mouseout',function () {
        _nav()
    })
    $('header.active nav li:not(.bg)').on('mouseover',function () {
        var _index =$(this).index()
        console.log()
        $('header.active nav li a').css('color','#333')
        $('header.active nav li.bg').css('top',_index*32+'px')
        $(this).find('a').css('color','white')
    })

    $('section.toolbar span.switch').on('click',function () {
        if($('section').hasClass('active')){
            $('section').removeClass('active')
        }else {
            $('section').addClass('active')
        }
        if($('header').hasClass('active')){
            $('header').removeClass('active')
        }else {
            $('header').addClass('active')
        }
        if($(this).hasClass('glyphicon-chevron-left')){
            $(this).removeClass('glyphicon-chevron-left')
            $(this).addClass('glyphicon-th-list')
        }else {
            $(this).removeClass('glyphicon-th-list')
            $(this).addClass('glyphicon-chevron-left')
        }
    })

    _aside()

})

function _aside() {
    _h = window.innerHeight - $('.artshow aside').innerHeight()
    if(_h>1){
        $('.artshow aside').css('top',_h/2+'px');
    }
}

function _nav() {
    $('header.active nav li a').css('color','#333')
    $('header.active nav li.active a').css('color','white')
    var _index =$('header.active nav li.active').index()
    $('header.active nav li.bg').css('top',_index*32+'px')
}