document.addEventListener('DOMContentLoaded', function () {
    alert('123')
    console.log('DOM đã load'); // Debug: DOMContentLoaded

    // Lấy tất cả form duyệt đơn CLB
    const forms = document.querySelectorAll('.club-request-handle-form');
    console.log('Tìm thấy', forms.length, 'form'); // Debug: số lượng form

    forms.forEach(form => {
        console.log('Xử lý form:', form); // Debug: form hiện tại
        const canvas = form.querySelector('.admin-signature-pad');
        const signatureInput = form.querySelector('input[name="admin_signature"]');
        const clearBtn = form.querySelector('.clear-signature');

        if (!canvas || !signatureInput) {
            console.log('Canvas hoặc input không tồn tại'); // Debug
            return;
        }

        const signaturePad = new SignaturePad(canvas);
        console.log('SignaturePad đã khởi tạo'); // Debug

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                console.log('Xóa chữ ký'); // Debug
                signaturePad.clear();
            });
        }

        form.addEventListener('submit', function(e) {
            console.log('Submit form'); // Debug: submit form
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                console.log('Chưa ký chữ ký'); // Debug
                alert('Vui lòng ký tên trước khi duyệt hoặc từ chối!');
            } else {
                console.log('Chữ ký đã có, lưu Base64'); // Debug
                signatureInput.value = signaturePad.toDataURL();
            }
        });
    });
});
