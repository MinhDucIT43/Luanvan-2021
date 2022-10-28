$('.deleteChucVu').on('click',function(e){
    e.preventDefault();
    var self = $(this);
    // console.log(self.data('title'));
    Swal.fire({
        title: 'Xoá chức vụ!',
        text: "Bạn chắc chắn muốn xoá chức vụ này?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Xoá',
        cancelButtonText: 'Huỷ',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire(
            'Đã xoá chức vụ!',
            'Chức vụ bạn muốn xoá đã bị xoá.',
            'success'
            )
            location.href = self.attr('href');
        }
    })
})