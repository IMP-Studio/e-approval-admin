<!DOCTYPE HTML
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
  xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
  </xml>
  <![endif]-->
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="x-apple-disable-message-reformatting">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>

  <style type="text/css">
    @media only screen and (min-width: 620px) {
      .u-row {
        width: 600px !important;
      }
      .u-row .u-col {
        vertical-align: top;
      }
      .u-row .u-col-100 {
        width: 600px !important;
      }
    }
    
    @media (max-width: 620px) {
      .u-row-container {
        max-width: 100% !important;
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
      .u-row .u-col {
        min-width: 320px !important;
        max-width: 100% !important;
        display: block !important;
      }
      .u-row {
        width: calc(100% - 40px) !important;
      }
      .u-col {
        width: 100% !important;
      }
      .u-col>div {
        margin: 0 auto;
      }
    }
    
    body {
      margin: 0;
      padding: 0;
    }
    
    table,
    tr,
    td {
      vertical-align: top;
      border-collapse: collapse;
    }
    
    p {
      margin: 0;
    }
    
    .ie-container table,
    .mso-container table {
      table-layout: fixed;
    }
    
    * {
      line-height: inherit;
    }
    
    a[x-apple-data-detectors='true'] {
      color: inherit !important;
      text-decoration: none !important;
    }
    
    table,
    td {
      color: #000000;
    }
    
    a {
      color: #04a768;
      text-decoration: none;
    }
  </style>
</head>

<body class="clean-body u_body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #f9f9f9;color: #000000">
  <table style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #f9f9f9;width:100%" cellpadding="0" cellspacing="0">
    <tbody>
      <tr style="vertical-align: top">
        <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">

          {{-- IMAGE --}}
          <div class="u-row-container" style="padding: 0px;background-color: transparent">
            <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
              <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                  <div style="width: 100% !important;">
                    <div style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                      <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                        <tbody>
                          <tr>
                            <td style="overflow-wrap:break-word;word-break:break-word;padding:20px;font-family:'Cabin',sans-serif;" align="left">
                              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                  <td style="padding-right: 0px;padding-left: 0px;" align="center">
                                    <img align="center" border="0" src="https://admin.approval.impstudio.id/images/IMP-full.jpg" alt="Image" title="Image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 50%;max-width: 1000px;"
                                      width="179.2" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- CCONTENT EMAIL --}}
          <div class="u-row-container" style="padding: 0px; background-color: transparent;">
            <div class="u-row" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                <div style="border-collapse: collapse; display: table; width: 100%; background-color: transparent;">
                    <div class="u-col u-col-100" style="max-width: 320px; min-width: 600px; display: table-cell; vertical-align: top;">
                        <div style="width: 100% !important;">
                            <div style="padding: 0px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent;">
                                <div style="padding: 10px; text-align: start; background-color: #1B253B;">
                                  <p><br></p>
                                  @if ($presence->category == 'telework' || $presence->category == 'work_trip')
                                    {{-- PENGAJUAN PRESSENSI --}}

                                    <p style="color: #CBD5E1; font-family: Verdana; font-size: 12px;margin-bottom: 3px">
                                      <span style="font-weight: 600; font-size: 15px; color: #CBD5E1; text-align: start;text-transform:capitalize;">Hi, {{$user ->name}}.</span> 
                                    </p>
                                    @php
                                      if ($presence->category == 'telework') {
                                          $categoryPresence = 'Work From Anywhere';
                                      } elseif ($presence->category == 'work_trip') {
                                          $categoryPresence = 'Perjalanan Dinas';
                                      } else {
                                          $categoryPresence = '';
                                      }
                                    @endphp
                                    <p style="font-family: Verdana;">
                                      <span style="font-weight: 400; color: #CBD5E1; font-size: 13px;">Kamu telah mengajukan Presensi <span style="font-weight: bold">{{$categoryPresence}}</span></span>
                                    </p>
                                    <p style="font-family: Verdana;">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:initial;">Berikut merupakan detail lengkap pengajuan :</span>
                                    </p>
                                    <p style="font-family: Verdana;">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:capitalize;">Jenis Presensi : <span style="font-weight: bold">{{$categoryPresence}}</span></span><br>
                                        @if ($presence->category == 'telework' && !is_null($telework))
                                            <span style="font-weight: 400; color: #CBD5E1; font-size: 13px; text-transform: capitalize;">Kategori WFA : <span style="font-weight: bold">{{$telework->telework_category}}</span></span><br>
                                        @endif
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:capitalize;">Tanggal Pengajuan : <span style="font-weight: bold">{{ \Carbon\Carbon::parse($presence->date)->format('d F Y') }}</span></span>
                                    </p>
                                    <p><br></p>
                                    {{-- KALAU ADA WFA + other --}}
                                    @if ($presence->category == 'telework' && !is_null($telework) && $telework->category_description != null)
                                    <p style="font-family: Verdana;">
                                      <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:initial;">Deskripsi WFA :</span><br>
                                      <div style="padding: 10px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; background-color: #28334E; border-radius: 10px; margin-top: 10px">
                                          <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:initial;font-family: Verdana;text-transform: initial">{{$telework->category_description}}</span>
                                      </div>
                                    </p>
                                    <p><br></p>
                                    @endif
                                    {{-- Kalau ada file --}}
                                    @if($presence->category == 'work_trip' && !is_null($workTrip) && $workTrip != null)
                                    <p style="font-family: Verdana;">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:initial;margin-bottom: 3px">File terkait :</span><br>
                                        <a href="{{ config('app.url') }}/storage/{{ $workTrip->file }}" style="text-decoration: none; color: #CBD5E1;">
                                            <span style="padding: 10px; border: none; background-color: #28334E; border-radius: 10px; display: inline-block; cursor: pointer;">
                                                <span style="font-weight: 400; font-size: 13px; text-transform: initial; font-family: Verdana;">
                                                  <img align="center" border="0" src="https://admin.approval.impstudio.id/images/file.svg" alt="Image" title="Image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100%;max-width: 25px;"/> 
                                                    File Work Trip
                                                </span>
                                            </span>
                                        </a>
                                    </p>
                                    <p><br></p>
                                    @endif
                                    @php
                                      if ($presence->category == 'telework' && $telework != null) {
                                          $status = $telework->statusCommit->first()->status;
                                      } elseif ($presence->category == 'work_trip' && $workTrip != null) {
                                          $status = $workTrip->statusCommit->first()->status;
                                      } else {
                                          $status = 'Unknown';
                                      }
                                    @endphp
                                    <p style="font-family: Verdana;">
                                    @php
                                      if ($presence->category == 'telework' && $telework != null) {
                                          $approverName = $telework->statusCommit->first()->approver->name;
                                      } elseif ($presence->category == 'work_trip' && $workTrip != null) {
                                          $approverName = $workTrip->statusCommit->first()->approver->name;
                                      } else {
                                          $approverName = 'Unknown';
                                      }
                                    @endphp
                                    <p style="font-family: Verdana;">
                                      <span style="font-weight: 400; color: #CBD5E1 ;font-size: 12px;text-transform:capitalize">Status Pengajuan : <span style="font-weight: bold">{{$status}}</span></span>
                                    </p>
                                    <p style="font-family: Verdana;">
                                      <span style="font-weight: 400; color: #CBD5E1 ;font-size: 12px;text-transform:initial;">Nama Approver : <span style="font-weight: bold">{{$approverName}}</span></span><br>
                                    </p>
                                    @php
                                      if ($presence->category == 'telework' && $telework != null) {
                                          $statusDesc = $telework->statusCommit->first()->description;
                                      } elseif ($presence->category == 'work_trip' && $workTrip != null) {
                                          $statusDesc = $workTrip->statusCommit->first()->description;
                                      } else {
                                          $statusDesc = 'Unknown';
                                      }
                                    @endphp
                                    <p style="font-family: Verdana;">
                                      <span style="font-weight: 400; color: #CBD5E1 ;font-size: 12px;text-transform:initial;">Deskripsi Approval :</span><br>
                                      <div style="padding: 10px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; background-color: #28334E; border-radius: 10px; margin-top: 10px">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 12px;text-transform:initial;font-family: Verdana;text-transform: initial">{{$statusDesc}}</span>
                                      </div>
                                    </p>

                                  @elseif($presence->category == 'leave')
                                    {{-- INI PENGAJUAN CUTI --}}

                                    <p style="color: #CBD5E1; font-family: Verdana; font-size: 12px;margin-bottom: 3px">
                                      <span style="font-weight: 600; font-size: 15px; color: #CBD5E1; text-align: start;text-transform:capitalize;">Hi, {{$user->name}}.</span> 
                                    </p>
                                    <p><br></p>
                                    @php
                                      if($leave != null){
                                        if ($leave->leavedetail->typeOfLeave->leave_name == 'yearly') {
                                            $categoryLeave = 'Tahunan';
                                        } elseif ($leave->leavedetail->typeOfLeave->leave_name == 'exclusive') {
                                            $categoryLeave = 'Khusus';
                                        } elseif ($leave->leavedetail->typeOfLeave->leave_name == 'emergency') {
                                            $categoryLeave = 'Darurat';
                                        } else {
                                            $categoryLeave = '';
                                        }
                                      }
                                    @endphp
                                    <p style="font-family: Verdana;">
                                      <span style="font-weight: 400; color: #CBD5E1; font-size: 13px;">Kamu telah mengajukan Cuti <span style="font-weight: bold">{{$categoryLeave}}</span></span>
                                    </p>
                                    <p style="font-family: Verdana;">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:capitalize">Berikut merupakan detail lengkap pengajuan :</span>
                                    </p>
                                    <p style="font-family: Verdana;">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:capitalize">Jenis Cuti : <span style="font-weight: bold">{{$categoryLeave}}</span></span><br>
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:capitalize">Detail Cuti : <span style="font-weight: bold">{{$leave->leavedetail->description_leave}}</span></span><br>
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:capitalize">Tanggal Mulai & Akhir : <span style="font-weight: bold">{{\Carbon\Carbon::parse($leave->start_date)->format('d F Y')}} - {{\Carbon\Carbon::parse($leave->end_date)->format('d F Y')}} ({{$leave->total_leave_days}} Hari)</span></span><br>
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:capitalize">Tanggal Masuk : <span style="font-weight: bold">{{\Carbon\Carbon::parse($leave->entry_date)->format('d F Y')}}</span></span><br>
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:capitalize">Tanggal Pengajuan : <span style="font-weight: bold">{{\Carbon\Carbon::parse($presence->created_at)->format('d F Y H:i A')}}</span></span>
                                    </p>
                                    <p><br></p>
                                    {{-- KALAU ADA FILE --}}
                                    @if($presence->category == 'leave' && $leave != null && $leave->file != '' && $leave->file != null)
                                    <p style="font-family: Verdana;">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 13px;text-transform:initial;margin-bottom: 3px">File terkait :</span><br>
                                        <a href="{{ config('app.url') }}/storage/{{ $leave->file }}" style="text-decoration: none; color: #CBD5E1;">
                                            <span style="padding: 10px; border: none; background-color: #28334E; border-radius: 10px; display: inline-block; cursor: pointer;">
                                                <span style="font-weight: 400; font-size: 13px; text-transform: initial; font-family: Verdana;">
                                                  <img align="center" border="0" src="https://admin.approval.impstudio.id/images/file.svg" alt="Image" title="Image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100%;max-width: 25px;"/> 
                                                    File Cuti 
                                                </span>
                                            </span>
                                        </a>
                                    </p>
                                    <p><br></p>
                                    @endif
                                    <p style="font-family: Verdana;">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 12px;text-transform:capitalize">Status Pengajuan : <span style="font-weight: bold">{{$leave->statusCommit->first()->status}}</span></span>
                                    </p>
                                    <p style="font-family: Verdana;"> 
                                      <span style="font-weight: 400; color: #CBD5E1 ;font-size: 12px;text-transform:initial;">Nama Approver : <span style="font-weight: bold">{{$leave->statusCommit->first()->approver->name}}</span></span><br>
                                    </p>
                                    <p style="font-family: Verdana;">
                                      <span style="font-weight: 400; color: #CBD5E1 ;font-size: 12px;text-transform:initial;">Deskripsi Approval :</span><br>
                                      <div style="padding: 10px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; background-color: #28334E; border-radius: 10px; margin-top: 10px">
                                        <span style="font-weight: 400; color: #CBD5E1 ;font-size: 12px;text-transform:initial;font-family: Verdana;text-transform: initial">{{$leave->statusCommit->first()->description}}</span>
                                      </div>
                                    </p>
                                  @endif
                                  <p><br></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>

        {{-- FOOTER EMAIL --}}
        <div class="u-row-container" style="padding: 0px;background-color: transparent">
            <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #28334E;">
                <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
                    <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                        <div style="width: 100% !important;">
                            <div style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                <table style="font-family:'Cabin',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                    <tbody>
                                        <tr>
                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family: Verdana;;" align="left">
                                                <div style="color: #CBD5E1; line-height: 180%; text-align: center; word-wrap: break-word;">
                                                    <p style="font-size: 14px; line-height: 180%;"><span style="font-size: 16px; line-height: 28.8px;">E-Approval • {{ \Carbon\Carbon::parse($presence->date)->format('d F Y') }}</span></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </td>
      </tr>
    </tbody>
  </table>
</body>

</html>