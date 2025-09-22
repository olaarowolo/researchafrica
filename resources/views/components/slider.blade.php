<style>

    /* Hide the images by default */
    .mySlides {
        display: none;
    }
    /* Fading animation */
    .fade {
        animation-name: fade;
        animation-duration: 8s;
    }

    @keyframes fade {
        from {
            opacity: .8
        }

        to {
            opacity: 1
        }
    }

</style>

<div class="">
    <!-- Slideshow container -->
        <!-- Full-width images with number and caption text -->
        <div class="mySlides fade">
            <img src="{{ request()->url().'/images/Slide1.png' }}" style="width:100%">

        </div>

        <div class="mySlides fade">
            <img src="{{ request()->url().'/images/Slide2.png' }}" style="width:100%">
        </div>

        <div class="mySlides fade">
            <img src="{{ request()->url().'/images/Slide3.png' }}" style="width:100%">
        </div>
</div>

<script>
    let slideIndex = 0;
showSlides();

function showSlides() {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slideIndex++;
  if (slideIndex > slides.length) {slideIndex = 1}
  slides[slideIndex-1].style.display = "block";
  setTimeout(showSlides, 5000); // Change image every 2 seconds
}
</script>
