
<template >
    <slider :items="items"  :speed="1" :delay="3" :pause="true" :autoplay="true" :dots="true"  :arrows="true">
    </slider>
</template>
<script>
  import Slider from 'vue-slider'
  export default {
    data () {
      return {
        items: [
          {
            src: 'http://placehold.it/350x150/999999/000000',
            alt: 'images-1111'
          },
          {
            src: 'http://placehold.it/350x150/eeeeee/c90023',
            alt: 'images-2'
          },
          {
            src: 'http://placehold.it/350x150/333333/eeeeee',
            alt: 'images-3'
          },
          {
            src: 'http://placehold.it/350x150/333333/eeeeee',
            alt: 'images-3'
          }
        ]
      }
    },
    components: {
      Slider
    }
  }
</script>
