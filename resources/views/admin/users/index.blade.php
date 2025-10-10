@extends('admin.layouts.app')

  @section('content')
      <div class="container mt-5">
          <h2 class="mb-4">Danh sách Người dùng</h2>

          <!-- Form tìm kiếm và lọc -->
          <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
              <div class="row g-3">
                  <div class="col-md-4">
                      <input type="text" name="name" class="form-control" placeholder="Tên..." value="{{ request('name') }}">
                  </div>
                  <div class="col-md-4">
                      <input type="text" name="email" class="form-control" placeholder="Email..." value="{{ request('email') }}">
                  </div>
                  <div class="col-md-3">
                      <select name="role" class="form-select">
                          <option value="">Tất cả vai trò</option>
                          <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                          <option value="club_manager" {{ request('role') == 'club_manager' ? 'selected' : '' }}>Quản lý CLB</option>
                          <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Thành viên</option>
                      </select>
                  </div>
                  <div class="col-md-1">
                      <button type="submit" class="btn btn-primary w-100">Tìm</button>
                  </div>
              </div>
          </form>

          <!-- Bảng Bootstrap -->
          <div class="table-responsive">
              <table class="table table-striped table-hover">
                  <thead class="table-dark">
                      <tr>
                          <th>ID</th>
                          <th>Tên</th>
                          <th>Email</th>
                          <th>Vai trò</th>
                          <th>Trạng thái</th>
                          <th>Hành động</th>
                      </tr>
                  </thead>
                  <tbody>
                      @forelse ($users as $user)
                          <tr>
                              <td>{{ $user->id }}</td>
                              <td>{{ $user->name }}</td>
                              <td>{{ $user->email }}</td>
                              <td>{{ $user->role }}</td>
                              <td>
                                  <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                      {{ $user->status === 'active' ? 'Hoạt động' : 'Khóa' }}
                                  </span>
                              </td>
                              <td>
                                  <button class="btn btn-info btn-sm toggle-status" data-id="{{ $user->id }}"
                                          data-status="{{ $user->status }}">
                                      {{ $user->status === 'active' ? 'Khóa' : 'Mở khóa' }}
                                  </button>
                                  <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                  <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                  </form>
                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="6" class="text-center">Không có dữ liệu.</td>
                          </tr>
                      @endforelse
                  </tbody>
              </table>
          </div>

          <!-- Phân trang -->
          @if ($users->hasPages())
              <div class="d-flex justify-content-center">
                  {{ $users->links('pagination::bootstrap-5') }}
              </div>
          @endif
      </div>

      <!-- Script cho AJAX -->
      <script>
          document.addEventListener('DOMContentLoaded', function () {
              document.querySelectorAll('.toggle-status').forEach(button => {
                  button.addEventListener('click', function () {
                      const userId = this.getAttribute('data-id');
                      const currentStatus = this.getAttribute('data-status');

                      fetch(`{{ route('admin.users.toggleStatus', ['user' => ':id']) }}`.replace(':id', userId), {
                          method: 'POST',
                          headers: {
                              'X-CSRF-TOKEN': '{{ csrf_token() }}',
                              'Content-Type': 'application/json',
                          },
                          body: JSON.stringify({ status: currentStatus }),
                      })
                      .then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              // Cập nhật giao diện
                              const badge = this.parentElement.previousElementSibling.querySelector('.badge');
                              badge.textContent = data.status === 'active' ? 'Hoạt động' : 'Khóa';
                              badge.className = `badge ${data.status === 'active' ? 'badge-success' : 'badge-danger'}`;
                              this.textContent = data.status === 'active' ? 'Khóa' : 'Mở khóa';
                              this.setAttribute('data-status', data.status);

                              // Hiển thị thông báo
                              alert(data.message);
                          } else {
                              alert('Có lỗi xảy ra. Vui lòng thử lại.');
                          }
                      })
                      .catch(error => {
                          console.error('Error:', error);
                          alert('Có lỗi xảy ra khi gửi yêu cầu.');
                      });
                  });
              });
          });
      </script>
  @endsection