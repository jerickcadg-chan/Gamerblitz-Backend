<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>No Handphone</th>
            <th>Tgl Daftar</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $index => $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone_number }}</td>
                <td>{{ parse_date_time($user->created_at) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="100%" class="text-center">Tidak ada data ditemukan</td>
            </tr>
        @endforelse
    </tbody>
</table>
