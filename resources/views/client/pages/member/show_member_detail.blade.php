@extends('client.layouts.app')
@section('title', 'Chi tiết thành viên')

@section('content')
<div class="container mt-4">
    <h3>Chi tiết thành viên: {{ $clubMember->member->user->name }}</h3>
    <div class="row mt-4">
        <div class="col-md-3 text-center">
            @php
    $avatar = $clubMember->member->user->avatar ?? null;

    if ($avatar && !Str::startsWith($avatar, 'http')) {
        $avatar = Storage::url($avatar);
    }

    $avatar = $avatar ?? asset('default-avatar.png');
@endphp

<img src="{{ $avatar }}" class="rounded-circle mb-3" width="80" height="80">


            <p><strong>Role trong CLB:</strong> {{ ucfirst($clubMember->role) }}</p>
            <p><strong>Trạng thái:</strong> {{ ucfirst($clubMember->status) }}</p>
            <p><strong>Ngày tham gia:</strong> {{ \Carbon\Carbon::parse($clubMember->joined_at)->format('d/m/Y') }}</p>
        </div>
        <div class="col-md-9">
            <h5>Thông tin cá nhân</h5>
            <table class="table table-bordered">
                <tr>
                    <th>Tên đầy đủ</th>
                    <td>{{ $clubMember->member->user->name }}</td>
                </tr>
                <tr>
                    <th>Mã sinh viên</th>
                    <td>{{ $clubMember->member->student_code ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Giới tính</th>
                    <td>
                        @php
                            $gender = $clubMember->member->gender ?? '';
                            $genderText = match($gender) {
                                'male' => 'Nam',
                                'female' => 'Nữ',
                                'other' => 'Khác',
                                default => '-'
                            };
                        @endphp
                        {{ $genderText }}
                    </td>
                </tr>
                <tr>
                    <th>Tuổi</th>
                    <td>
                        @if($clubMember->member->date_of_birth)
                            {{ \Carbon\Carbon::parse($clubMember->member->date_of_birth)->age }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Ngày sinh</th>
                    <td>{{ $clubMember->member->date_of_birth ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Khóa học</th>
                    <td>{{ $clubMember->member->course ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Chuyên ngành</th>
                    <td>{{ $clubMember->member->major ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td>{{ $clubMember->member->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Địa chỉ</th>
                    <td>{{ $clubMember->member->address ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Số CCCD</th>
                    <td>{{ $clubMember->member->citizen_id ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Ngày cấp CCCD</th>
                    <td>{{ $clubMember->member->issued_date ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Nơi cấp CCCD</th>
                    <td>{{ $clubMember->member->issued_place ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dân tộc</th>
                    <td>{{ $clubMember->member->ethnicity ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Ghi chú</th>
                    <td>{{ $clubMember->note ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
