/*
 * jQuery FlexSlider v1.0 (Release)
 * http://flex.madebymufffin.com
 *
 * Copyright 2011, Tyler Smith
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Перевод: команда проекта RUSELLER.COM
 * http://www.ruseller.com
 *
 */


(function ($) {
  $.fn.extend({
    flexslider: function(options) {
      //Опции плагина и значения по умолчанию
      var defaults = {
        animation: "fade",              //Выбор типа анимации (fade/slide)
        slideshow: true,                //Будет ли слайдер выполнять анимацию автоматически по умолчанию? (true/false)
				slideshowSpeed: 7000,           //Скорость цикла слайдшоу, в миллисекундах
				animationDuration: 500,         //Скорость анимации смены кадра, в миллисекундах
				directionNav: true,             //Создавать навигацию для переключения на следующий/предыдущий кадр? (true/false)
				controlNav: true,               //Создавать навигацию для контроля страниц? (true/false)
				keyboardNav: true,              //Допуск контроля с клавиатуры с помощью клавиш влево/вправо (true/false)
				prevText: "Previous",           //Текст для пункта навигации "предыдущий"
				nextText: "Next",               //Текст для пункта навигации "следующий"
				slideToStart: 0,                //Номер слайда, с которого начинается слайдшоу. Исопльзуется правила для нумерации элементов массива (0 = первый слайд)
				pauseOnAction: true,            //Пауза слайдшоу при использовании упаравлюших элементов. Очень рекомендуется использовать. (true/false)
				pauseOnHover: false,            //Пауза слайдшоу при наведении курсора мыши на слайдер, затем проигрывание продолжается (true/false)
				controlsContainer: ""           //Дополнительное свойство: можно объявить контейнер для элементов навигации. По умолчанию будет добавлен элемент flexSlider. Если заданный элемент не будет найден, то будут использоваться значения по умолчанию.
			}
			
			//Получаем слайдер, слайды. Устанавливаем другие полезные переменные.
			var options =  $.extend(defaults, options),
			    slider = $(this),
			    container = $('.slides', slider),
			    slides = $('.slides li', slider),
			    ANIMATING = false,
          currentSlide = options.slideToStart;
      
      //инициализация анимации слайдера
      if (options.animation.toLowerCase() == "slide") {
        slides.show(); 
        slider.css({"overflow": "hidden"});
        slides.width(slider.width()).css({"float": "left"});
        container.width((slides.length * slider.width()) + 200); //Дополнительная ширина необходима для работы слайдера
      } else { //По умолчанию используется затухание
        slides.hide().eq(currentSlide).fadeIn(400);
      }
      	
    	///////////////////////////////////////////////////////////////////
    	// FLEXSLIDER: Типа анимации
    	function flexAnimate(target) {
        if (!ANIMATING) {
          ANIMATING = true;
          if (options.animation.toLowerCase() == "slide") {
      	    container.animate({"margin-left": (-1 * target)* slider.width() + "px"}, options.animationDuration, function(){
      	      ANIMATING = false;
      	      currentSlide = target;
      	    });
        	} else { //по умолчанию: затухание
        	  slider.css({"minHeight": slides.eq(currentSlide).height()});
      	    slides.eq(currentSlide).fadeOut(options.animationDuration, function() {
              slides.eq(target).fadeIn(options.animationDuration, function() {
                ANIMATING = false;
                currentSlide = target;
              });
              slider.css({"minHeight": "inherit"});
            });
        	}
      	}
  	  }
    	///////////////////////////////////////////////////////////////////
    	
    	///////////////////////////////////////////////////////////////////
    	// FLEXSLIDER: Управляющая навигация
      if (options.controlNav) {
        if (slides.size() > 1) {
          var controlNav = $('<ol class="flex-control-nav"></ol>');
          var j = 1;
          for (var i = 0; i < slides.size(); i++) {
            controlNav.append('<li><a>' + j + '</a></li>');
            j++;
          }

          if ($(options.controlsContainer).length > 0) {
            $(options.controlsContainer).append(controlNav);
          } else {
            slider.append(controlNav);
          }
        }
        
        controlNav = $('.flex-control-nav li a');
        controlNav.eq(currentSlide).addClass('active');

        controlNav.click(function(event) {
          event.preventDefault();
          if ($(this).hasClass('active') || ANIMATING) {
            return;
          } else {

            controlNav.removeClass('active');
            $(this).addClass('active');
            
            var selected = controlNav.index($(this));
            flexAnimate(selected);
            if (options.pauseOnAction) {
              clearInterval(animatedSlides);
            }
          }
        });
      }
      ///////////////////////////////////////////////////////////////////
      
      //////////////////////////////////////////////////////////////////
      //FLEXSLIDER: Навигация направления
      if (options.directionNav) {
        if ($(options.controlsContainer).length > 0) {
            $(options.controlsContainer).append($('<ul class="flex-direction-nav"><li><a class="prev" href="#">' + options.prevText + '</a></li><li><a class="next" href="#">' + options.nextText + '</a></li></ul>'));
          } else {
            slider.append($('<ul class="flex-direction-nav"><li><a class="prev" href="#">' + options.prevText + '</a></li><li><a class="next" href="#">' + options.nextText + '</a></li></ul>'));
          }
      
      	$('.flex-direction-nav li a').click(function(event) {
      	  event.preventDefault();
      	  if (ANIMATING) {
      	    return;
      	  } else {
        	  
        	  if ($(this).hasClass('next')) {
        	    var target = (currentSlide == slides.length - 1) ? 0 : currentSlide + 1;
        	  } else {
        	    var target = (currentSlide == 0) ? slides.length - 1 : currentSlide - 1;
        	  }
            
            if (options.controlNav) {
          	  controlNav.removeClass('active');
          	  controlNav.eq(target).addClass('active');
      	    }
      	    
        	  flexAnimate(target);
        	  if (options.pauseOnAction) {
              clearInterval(animatedSlides);
            }
          }
      	});
      }
    	//////////////////////////////////////////////////////////////////

      //////////////////////////////////////////////////////////////////
      //FLEXSLIDER: Навигация с клавиатуры
      if (options.keyboardNav) {
        $(document).keyup(function(event) {
          if (ANIMATING) {
            return;
          } else if (event.keyCode != 39 && event.keyCode != 37){
            return;
          } else {
            
            if (event.keyCode == 39) {
        	    var target = (currentSlide == slides.length - 1) ? 0 : currentSlide + 1;
        	  } else if (event.keyCode == 37){
        	    var target = (currentSlide == 0) ? slides.length - 1 : currentSlide - 1;
        	  }
      	  
        	  if (options.controlNav) {
          	  controlNav.removeClass('active');
          	  controlNav.eq(target).addClass('active');
      	    }
      	  
        	  flexAnimate(target);
        	  if (options.pauseOnAction) {
              clearInterval(animatedSlides);
            }
          }
        });
      }
    	//////////////////////////////////////////////////////////////////

			//////////////////////////////////////////////////////////////////
      //FLEXSLIDER: Анимация слайдшоу
      if (options.slideshow) {
        var animatedSlides;
        
        function animateSlides() {
          if (ANIMATING) {
            return;
          } else {
        	  var target = (currentSlide == slides.length - 1) ? 0 : currentSlide + 1;
      	  
        	  if (options.controlNav) {
          	  controlNav.removeClass('active');
          	  controlNav.eq(target).addClass('active');
      	    }
      	  
        	  flexAnimate(target);
          }
        }
        
        // Пауза при наведении курсора мыши
        if (options.pauseOnHover) {
          slider.hover(function() {
            clearInterval(animatedSlides);
          }, function() {
            animatedSlides = setInterval(animateSlides, options.slideshowSpeed);
          });
        }
        
        // Инициализация анимации
        if (slides.length > 1) {
          animatedSlides = setInterval(animateSlides, options.slideshowSpeed);
        }
      }
    	//////////////////////////////////////////////////////////////////
    	
    	//////////////////////////////////////////////////////////////////
      //FLEXSLIDER: Функция изменения размера (если нужно)
      if (options.animation.toLowerCase() == "slide") {
        var sliderTimer;
        $(window).resize(function(){
          slides.width(slider.width());
          container.width((slides.length * slider.width()) + 200); //Дополнительная ширина необходима для работы слайдера
          
          clearTimeout(sliderTimer);
          sliderTimer = setTimeout(function(){
            flexAnimate(currentSlide);
          }, 300);
        });
      }
      //////////////////////////////////////////////////////////////////
	  }
  });
  
})(jQuery);