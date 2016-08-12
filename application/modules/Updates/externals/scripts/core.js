/* $Id: core.js 2010-07-30 18:00 mirlan $ */

function paginator(pages){
  this.current = pages.current;
  this.first = pages.first;
  this.last = pages.last;

  this.previous = function(name){
    if (this.current == this.first){
      return false;
    }

    var $current = $(name+'_'+this.current);
    if ($current != undefined ){
      $current.removeClass('selected');
    }

    if (this.current == this.last){
      $(name+'_next').getChildren('span').setStyle('display', 'none');
      $(name+'_next').getChildren('a').setStyle('display', '');
    }

    this.current--;
    this.replace(name, 'stop');

    $(name+'_'+this.current).addClass('selected');

    if (this.current == this.first)
    {
      $(name+'_previous').getChildren('a').setStyle('display', 'none');
      $(name+'_previous').getChildren('span').setStyle('display', '');
    }
  }

  this.page = function(name, page) {
    if (page == this.current) {
      return false;
    }

    var $current = $(name+'_'+this.current);
    if ($current != undefined ) {
      $current.removeClass('selected');
    }
    $(name+'_'+page).addClass('selected');

    if ($('tasks_page') != null) {
      $('tasks_page').set('html', page);
    }

    if (this.first == page)
    {
      $(name+'_previous').getChildren('a').setStyle('display', 'none');
      $(name+'_previous').getChildren('span').setStyle('display', '');
    } else {
      $(name+'_previous').getChildren('span').setStyle('display', 'none');
      $(name+'_previous').getChildren('a').setStyle('display', '');
    }

    if (this.last == page)
    {
      $(name+'_next').getChildren('a').setStyle('display', 'none');
      $(name+'_next').getChildren('span').setStyle('display', '');
    } else {
      $(name+'_next').getChildren('span').setStyle('display', 'none');
      $(name+'_next').getChildren('a').setStyle('display', '');
    }
    this.current = page;
    this.replace(name, 'stop');
  }

  this.next = function(name){
    if (this.current == this.last){
      return false;
    }
    
    var $current = $(name+'_'+this.current);
    if ($current != undefined ){
      $current.removeClass('selected');
    }

    if (this.current == this.first){
      $(name+'_previous').getChildren('span').setStyle('display', 'none');
      $(name+'_previous').getChildren('a').setStyle('display', '');
    }

    this.current++;
    this.replace(name, 'stop');
    
    $(name+'_'+this.current).addClass('selected');

    if (this.current == this.last)
    {
      $(name+'_next').getChildren('a').setStyle('display', 'none');
      $(name+'_next').getChildren('span').setStyle('display', '');
    }
  },

  this.replace = function(name){
    if (name == 'tasks_paginator') {
      new Request.JSON({
        'url':'admin/updates/tasks/pagination/'+this.current,
        'method':'post',
        'data':{'format':'json', 'noCache':Math.random(), 'type':name, 'page':this.current},
        'onRequest':function(){
          $(name+'_items').fade('out');
        },
        'onSuccess':function(resp){
          if (resp.html){
            $(name+'_items').getChildren('table').destroy();
            $(name+'_items').set('html',resp.html);
          }
        },
        'onComplete':function(){
          $(name+'_items').fade('in');
        }
      }).send();
    }
    else {
      new Request.JSON({
        'url':'admin/updates/campaign/campaigns/'+this.current,
        'method':'post',
        'data':{'format':'json', 'noCache':Math.random(), 'type':name},
        'onRequest':function(){
          $(name+'_items').fade('out');
        },
        'onSuccess':function(resp){
          if (resp.html){
            $(name+'_items').getChildren('table').destroy();
            $(name+'_items').set('html',resp.html);
          }
        },
        'onComplete':function(){
          $(name+'_items').fade('in');
        }
      }).send();
    }
  }
}