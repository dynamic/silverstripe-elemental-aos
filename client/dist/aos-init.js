/* global AOS */
(function initAOS() {
  function start() {
    if (typeof AOS === 'undefined') {
      return;
    }

    // Below the tablet breakpoint every animation becomes a plain fade-up, avoiding
    // janky directional/flip effects and horizontal-scroll risk. The editor's choice is
    // stashed so crossing back above the breakpoint restores it. Only runs when the
    // breakpoint side changes, not on every resize event (mobile browsers fire resize
    // as the address bar shows/hides).
    var isMobileWidth = null;
    var started = false;
    function normalizeForBreakpoint() {
      var nowMobile = window.innerWidth < 992;
      if (nowMobile === isMobileWidth) {
        return;
      }
      isMobileWidth = nowMobile;

      document.querySelectorAll('[data-aos]').forEach(function each(el) {
        if (nowMobile) {
          if (!el.hasAttribute('data-aos-original')) {
            el.setAttribute('data-aos-original', el.getAttribute('data-aos'));
          }
          el.setAttribute('data-aos', 'fade-up');
        } else if (el.hasAttribute('data-aos-original')) {
          el.setAttribute('data-aos', el.getAttribute('data-aos-original'));
        }
      });
      if (started) {
        AOS.refreshHard();
      }
    }
    normalizeForBreakpoint();
    started = true;
    AOS.init({
      duration: 1200,
      once: true,
      // Strips data-aos handling entirely for visitors who asked for reduced motion.
      disable: function reducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      },
    });
    window.addEventListener('resize', normalizeForBreakpoint);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
}());
