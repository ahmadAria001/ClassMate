<script lang="ts">
    import Layout from "./Layout.svelte";
    import {
        Card,
        Table,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
    } from "flowbite-svelte";

    let announcement = [
        {
            title: "Perekrutan Anggota KPPS",
            desc: "Pengumuman bagi seluruh masyarakat, dengan adanya Pemilihan Umum yang akan segera dilaksanakan, bagi yang berminat untuk menjadi anggota KPPS untuk mengambil form pendaftaran di rumah Ketua RW 03.",
        },
        {
            title: "Pemilihan Calon ketua RT",
            desc: "Pengumuman bagi seluruh masyarakat, dengan adanya Pemilihan Umum yang akan segera dilaksanakan, dimohon warga untuk berkumpul di rumah ketua RT masing-masing",
        },
        {
            title: "Pengumuman 3",
            desc: "Pengumuman bagi seluruh masyarakat, diharap tetap tabah",
        },
        {
            title: "Pengumuman 4",
            desc: "Pengumuman bagi seluruh masyarakat, kalian terbaik",
        },
    ];

    let citizenActivity = [
        {
            id: 1,
            nama: "Kerja Bakti",
            lokasi: "JI. Rambutan - JI Mangga",
            waktu: "08.00 - 10.00",
            tanggal: "23 April 2024",
        },
        {
            id: 2,
            nama: "Rapat Karang Taruna",
            lokasi: "Rumah Adit, JI. Manggis No. 8",
            waktu: "19.00 - 20.00",
            tanggal: "25 April 2024",
        },
    ];
    let caColumn =
        citizenActivity.length > 0 ? Object.keys(citizenActivity[0]) : [];
</script>

<Layout>
    <Card class="max-w-full">
        <h5
            class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
        >
            Pengumuman
        </h5>
        {#each announcement.slice(0, 3) as { title, desc } (title)}
            <h5 class="font-bold mt-2 text-black dark:text-white">{title}</h5>
            <p class="mb-2">{desc}</p>
            <hr />
        {/each}
    </Card>
    <Card class="mt-3 max-w-full">
        <h5
            class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
        >
            Agenda Kegiatan Masyarakat
        </h5>
        <Table>
            <TableHead>
                <!-- {#each caColumn as columns}
                    <TableHeadCell>{columns}</TableHeadCell>
                {/each} -->
                <TableHeadCell>Nama</TableHeadCell>
                <TableHeadCell>Lokasi</TableHeadCell>
                <TableHeadCell>Waktu</TableHeadCell>
                <TableHeadCell>Tanggal</TableHeadCell>
            </TableHead>
            {#each citizenActivity as { id, nama, lokasi, waktu, tanggal } (id)}
                <TableBody>
                    <!-- <TableBodyCell>{id}</TableBodyCell> -->
                    <TableBodyCell>{nama}</TableBodyCell>
                    <TableBodyCell>{lokasi}</TableBodyCell>
                    <TableBodyCell>{waktu}</TableBodyCell>
                    <TableBodyCell>{tanggal}</TableBodyCell>
                </TableBody>
            {/each}
        </Table>
    </Card>
</Layout>
