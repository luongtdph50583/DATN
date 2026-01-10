<table class="table table-bordered">
    <thead>
        <tr>
            <th>STT</th>
            <th>Avatar</th>
            <th>Tên</th>
            <th>Mã sinh viên</th>
            <th>Tuổi</th>
            <th>Giới tính</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($members as $index => $member)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <img src="{{ $member->user->avatar ?? asset('default-avatar.png') }}" width="40" class="rounded-circle">
                </td>
                <td>{{ $member->user->name }}</td>
                <td>{{ $member->user->memberInfo->student_code ?? '-' }}</td>
                <td>
                    @if($member->user->memberInfo && $member->user->memberInfo->date_of_birth)
                        {{ \Carbon\Carbon::parse($member->user->memberInfo->date_of_birth)->age }}
                    @endif
                </td>
                <td>
                    @php
                        $gender = $member->user->memberInfo->gender ?? '';
                        $genderText = match($gender) {
                            'male' => 'Nam',
                            'female' => 'Nữ',
                            'other' => 'Khác',
                            default => '-'
                        };
                    @endphp
                    {{ $genderText }}
                </td>
                <td>
                    <a href="{{ route('club_manager.members.show', ['club' => $club->id, 'member' => $member->id]) }}" 
   class="btn btn-sm btn-primary">
   Xem chi tiết
</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Không tìm thấy thành viên nào</td>
            </tr>
        @endforelse
    </tbody>
</table>
