var slideShow = function(container, time) {
    container = document.getElementById(container);
    this.images = [];
    this.curImage = 0;
    for (i = 0; i < container.childElementCount; i++) {
      this.images.push(container.children[i]);
      this.images[i].style.opacity = 0;
    }
  
    // Handle going to to the next slide
    var nextSlide = function() {
      for (var i = 0; i < this.images.length; i++) {
        if (i!=this.curImage) this.images[i].style.opacity = 0;
      }
      this.images[this.curImage].style.opacity = 1;
      this.curImage++;
      if (this.curImage>=this.images.length) this.curImage=0;
      window.setTimeout(nextSlide.bind(document.getElementById(this)), time);
      // old code: window.setTimeout(nextSlide.bind(this), time);
    };
  
    nextSlide.call(this);
  
  };
  slideShow("slideshow", 8000);