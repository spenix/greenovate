export default function notify(t, m, type = 'primary') {
  $.notify({
    title: `<b>${t}</b>`,
    message: m
  },
    {
      type,
      allow_dismiss: true,
      newest_on_top: true,
      mouse_over: true,
      showProgressbar: false,
      spacing: 10,
      timer: 3000,
      placement: {
        from: 'top',
        align: 'right'
      },
      offset: {
        x: 30,
        y: 30
      },
      delay: 1000,
      z_index: 10000,
      animate: {
        enter: 'animated fadeInDown',
        exit: 'animated fadeOutUp'
      }
    });
}