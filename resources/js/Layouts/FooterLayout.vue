<script setup>
defineProps({
    systemSetup: {
        type: Object,
    },
});
</script>

<template>
    <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>{{ systemSetup?.nameShort }}</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="#">The Team</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
</template>
