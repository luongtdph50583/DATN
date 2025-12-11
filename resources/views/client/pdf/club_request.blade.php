{{-- resources/views/pdfs/club_request.blade.php --}}
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Đơn xin thành lập CLB</title>
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13pt;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-left {
            float: left;
            width: 50%;
            text-align: left;
            font-weight: bold;
            font-size: 11pt;
        }

        .header-right {
            float: right;
            width: 50%;
            text-align: right;
            font-weight: bold;
            font-size: 11pt;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin: 30px 0 10px 0;
            text-transform: uppercase;
        }

        .subtitle {
            text-align: center;
            font-style: italic;
            margin-bottom: 30px;
        }

        .content {
            text-align: justify;
            margin: 20px 0;
        }

        .content p {
            margin: 10px 0;
        }

        .section {
            margin: 20px 0;
        }

        .section-title {
            font-weight: bold;
            margin: 15px 0 10px 0;
        }

        .indent {
            margin-left: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 50px;
        }

        .signature-box {
            width: 45%;
            float: left;
            text-align: center;
        }

        .signature-box.right {
            float: right;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 80px;
        }

        .logo {
            text-align: center;
            margin: 20px 0;
        }

        .logo img {
            max-width: 150px;
            max-height: 150px;
        }

        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 200px;
        }

        .confirmation-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }

        .confirmation-item {
            margin: 5px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10pt;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header clearfix">
        <div class="header-left">
            TRƯỜNG CĐ FPT POLYTECHNIC<br>
            TRUNG TÂM FPT POLYTECHNIC ĐÀ NẴNG
        </div>
        <div class="header-right">
            CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>
            Độc lập - Tự do - Hạnh phúc
        </div>
    </div>

    <div style="clear: both; height: 20px;"></div>

    {{-- TITLE --}}
    <div class="title">ĐỜN XIN THÀNH LẬP</div>
    <div class="title">CÂU LẠC BỘ CLB</div>

    {{-- CONTENT --}}
    <div class="content">
        <p>Kính gửi: Phòng Công tác Sinh viên trường FPT Polytechnic Đà Nẵng</p>

        <p>Nhận hướng mọi người đều và nhấn đồng của trường, đồng thời góp phần tạo nên sân chơi giải trí lành mạnh, bổ
            ích cho sinh viên sau những giờ học tập căng thẳng.</p>
    </div>

    {{-- THÔNG TIN CƠ BẢN --}}
    <div class="section">
        <p><strong>Em là:</strong> <span class="dotted-line">{{ $request->creator->name }}</span>
            <strong>MSSV:</strong> <span
                class="dotted-line">{{ $request->creator->student->student_code ?? 'N/A' }}</span>
        </p>

        <p><strong>Nhằm hướng mọi người muốn thành lập CLB mang tên:</strong>
            <span class="dotted-line">{{ $request->name }}</span>
        </p>

        <p class="indent"><strong>1. Lĩnh vực hoạt động:</strong> {{ $request->field }}</p>
        <p class="indent"><strong>2. Mục đích hoạt động:</strong></p>
        <p class="indent" style="margin-left: 60px;">{{ $request->purpose }}</p>

        <p class="indent"><strong>3. Định hướng hoạt động:</strong></p>
        <p class="indent" style="margin-left: 60px;">{{ $request->description }}</p>
    </div>

    {{-- BAN CHỦ NHIỆM --}}
    <div class="section">
        <p class="indent"><strong>4. Ban chủ nhiệm (Câu lạc bộ: (Họ tên - MSSV)</strong></p>
        <table>
            <thead>
                <tr>
                    <th width="5%">STT</th>
                    <th width="30%">Chức vụ</th>
                    <th width="35%">Họ tên</th>
                    <th width="30%">MSSV</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $managementTeam = [
                        ['title' => 'Chủ nhiệm', 'user' => $request->clubManager],
                        ['title' => 'Phó chủ nhiệm', 'user' => $request->deputyManager],
                        ['title' => 'Thư ký', 'user' => $request->secretary],
                        ['title' => 'Thủ quỹ', 'user' => $request->treasurer],
                        ['title' => 'Quản lý sự kiện', 'user' => $request->eventManager],
                        ['title' => 'Phụ trách truyền thông', 'user' => $request->communication],
                    ];
                @endphp
                @foreach($managementTeam as $index => $member)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $member['title'] }}</td>
                        <td>{{ $member['user']->name ?? '' }}</td>
                        <td>{{ $member['user']->student->student_code ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- THÀNH VIÊN BAN ĐẦU --}}
    <div class="section">
        <p class="indent"><strong>5. Danh sách các thành viên (Họ tên - MSSV)</strong></p>
        <table>
            <thead>
                <tr>
                    <th width="10%">STT</th>
                    <th width="45%">Họ tên</th>
                    <th width="45%">MSSV</th>
                </tr>
            </thead>
            <tbody>
                @foreach($request->members as $index => $member)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->student->student_code ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- XÁC NHẬN THÀNH VIÊN --}}
    <div class="section">
        <p class="section-title">6. Xác nhận từ thành viên:</p>
        <div class="confirmation-box">
            @foreach($request->confirmations as $confirmation)
                <div class="confirmation-item">
                    <span>{{ $confirmation->user->name }}</span> -
                    <span>{{ $confirmation->user->student->student_code ?? 'N/A' }}</span>:
                    @if($confirmation->status)
                        <span class="status-badge status-confirmed">✓ Đã xác nhận</span>
                        <span style="font-size: 10pt;">({{ $confirmation->confirmed_at->format('d/m/Y H:i') }})</span>
                    @else
                        <span class="status-badge status-pending">⏳ Chưa xác nhận</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- THÔNG TIN BỔ SUNG --}}
    <div class="section">
        <p class="indent"><strong>+ Nội quy CLB:</strong></p>
        <p class="indent" style="margin-left: 60px;">{{ $request->rule }}</p>

        <p class="indent"><strong>+ File logo CLB:</strong> Đã đính kèm</p>
        <p class="indent"><strong>+ Kế hoạch hoạt động trong 3 tháng:</strong> Đã đính kèm (Phụ lục, mục 2)</p>
        <p class="indent"><strong>+ Đề án thành lập CLB (Phụ lục, mục 2):</strong></p>
        <p class="indent" style="margin-left: 60px;">+ Báo cáo hoạt động thử nghiệm (Tính trang CLB: số lượng thành
            viên, BCN, tần suất hoạt động, kho kết quả hoạt động về chất lượng và số lượng)</p>
    </div>

    {{-- PHÊ DUYỆT --}}
    @if($request->approvals->isNotEmpty())
        <div class="section">
            <p class="section-title">7. Phê duyệt từ Ban quản lý:</p>
            <div class="confirmation-box">
                @foreach($request->approvals as $approval)
                    <div class="confirmation-item">
                        <strong>{{ $approval->admin->name }}</strong>
                        @if($approval->status === 'approved')
                            <span class="status-badge status-confirmed">✓ Đã phê duyệt</span>
                        @else
                            <span class="status-badge" style="background-color: #f8d7da; color: #721c24;">✗ Từ chối</span>
                        @endif
                        <span style="font-size: 10pt;">({{ $approval->approved_at->format('d/m/Y H:i') }})</span>
                        @if($approval->comment)
                            <br>
                            <span style="font-style: italic;">Nhận xét: {{ $approval->comment }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- KẾT THÚC --}}
    <div class="content">
        <p>Thay mặt cho nhóm yêu thích tình lĩnh vực, em xin chấn thành cảm ơn!</p>
        <p>Chúng em xin chân thành cảm ơn các quy định pháp luật của Nhà nước, nội quy của Nhà trường và quy chế hoạt
            động của CLB.</p>
        <p>Kính mong Ban lãnh đạo nhà trường xem xét và phê duyệt cho CLB <strong>{{ $request->name }}</strong> được
            công nhận và chính thức hoạt động tại trường.</p>
        <p>Đại diện các thành viên CLB, em xin chân thành cảm ơn!</p>
        <p style="text-align: right; margin-top: 30px;">Trần trọng.</p>
    </div>

    {{-- CHỮ KÝ --}}
    <div class="signature-section clearfix">
        <div class="signature-box">
            <strong>Người phê duyệt</strong>
            <div class="signature-name">
                @if($request->approvals->isNotEmpty())
                    {{ $request->approvals->first()->admin->name }}
                @endif
            </div>
        </div>

        <div class="signature-box right">
            <strong>Người làm đơn</strong><br>
            <span style="font-style: italic; font-size: 11pt;">
                ......ngày.......tháng.......năm.......
            </span>
            <div class="signature-name">{{ $request->creator->name }}</div>
        </div>
    </div>

    {{-- LOGO --}}
    @if($request->logo)
        <div style="page-break-before: always;"></div>
        <div class="logo">
            <p><strong>PHỤ LỤC: LOGO CLB</strong></p>
            <img src="{{ public_path('storage/' . $request->logo) }}" alt="Logo CLB">
        </div>
    @endif

    {{-- FOOTER --}}
    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10pt; color: #666;">
        <p>Tài liệu được tạo tự động bởi hệ thống vào {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

</body>

</html>
