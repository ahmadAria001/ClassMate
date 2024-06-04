<!-- <script lang="ts">
    import { onMount } from "svelte";
    import Layout from "./Layout.svelte";
    import {
        Badge,
        Table,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        TableSearch,
        Button,
        Modal,
        Label,
        Input,
        ButtonGroup,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        ImageOutline,
    } from "flowbite-svelte-icons";
    let items = [
        {
            id: 1,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            noHp: "081234567890",
            desc: "Surat Kependudukan",
            status: "Dalam Proses",
        },
    ];
    let modalDetailReqBansos = false;
    let searchTerm = "";
    let currentPosition = 0;
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;

    const updateDataAndPagination = () => {
        const currentPageItems = items.slice(
            currentPosition,
            currentPosition + itemsPerPage,
        );
        renderPagination(currentPageItems.length);
    };

    const loadNextPage = () => {
        if (currentPosition + itemsPerPage < items.length) {
            currentPosition += itemsPerPage;
            updateDataAndPagination();
        }
    };

    const loadPreviousPage = () => {
        if (currentPosition - itemsPerPage >= 0) {
            currentPosition -= itemsPerPage;
            updateDataAndPagination();
        }
    };

    const renderPagination = (totalItems: number) => {
        totalPages = Math.ceil(items.length / itemsPerPage);
        const currentPage = Math.ceil((currentPosition + 1) / itemsPerPage);

        startPage = currentPage - Math.floor(showPage / 2);
        startPage = Math.max(1, startPage);
        endPage = Math.min(startPage + showPage - 1, totalPages);

        pagesToShow = Array.from(
            { length: endPage - startPage + 1 },
            (_, i) => startPage + i,
        );
    };

    const goToPage = (pageNumber: number) => {
        currentPosition = (pageNumber - 1) * itemsPerPage;
        updateDataAndPagination();
    };

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    onMount(() => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);
    });

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item.name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );
</script>

<Layout>
    <TableSearch
        placeholder="Cari Warga"
        hoverable={true}
        bind:inputValue={searchTerm}
        divClass="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden"
        innerDivClass="flex items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        classInput="text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2  pl-10"
    >
        <div
            slot="header"
            class="md:w-auto flex flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
        ></div>
        <TableHead>
            <TableHeadCell>Nama</TableHeadCell>
            <TableHeadCell>Alamat</TableHeadCell>
            <TableHeadCell>No. HP</TableHeadCell>
            <TableHeadCell>Keterangan</TableHeadCell>
            <TableHeadCell class="text-center">Status</TableHeadCell>
            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
        </TableHead>
        <TableBody>
            {#each filteredItems as item}
                <TableBodyRow>
                    <TableBodyCell>{item.name}</TableBodyCell>
                    <TableBodyCell>{item.address}</TableBodyCell>
                    <TableBodyCell>{item.noHp}</TableBodyCell>
                    <TableBodyCell>{item.name}</TableBodyCell>
                    {#if item.status == "Selesai"}
                        <TableBodyCell class="text-center">
                            <Badge color="green">{item.status}</Badge>
                        </TableBodyCell>
                    {:else if item.status == "Dalam Proses"}
                        <TableBodyCell class="text-center">
                            <Badge color="indigo">Menunggu</Badge>
                        </TableBodyCell>
                    {/if}
                    <TableBodyCell class="text-end">
                        <Button
                            color="blue"
                            on:click={() => {
                                modalDetailReqBansos = true;
                            }}>Detail</Button
                        >
                    </TableBodyCell>
                </TableBodyRow>
            {/each}
        </TableBody>

        <Modal
            title="Detail Pengajuan Bantuan Sosial"
            bind:open={modalDetailReqBansos}
            autoclose
        >
            <form method="POST">
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="fullName" class="mb-2">Nama Lengkap</Label>
                        <Input id="fullName" placeholder="Nama Lengkap" />
                    </div>
                    <div class="mb-4">
                        <Label for="bansos_type" class="mb-2"
                            >Jenis Bantuan</Label
                        >
                        <Input id="bansos_type" placeholder="Jenis Bantuan" />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="kk" class="mb-2">No KK</Label>
                        <Input id="kk" placeholder="No KK" />
                    </div>
                    <div class="mb-4">
                        <Label for="nik" class="mb-2">NIK</Label>
                        <Input id="nik" placeholder="NIK" />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="address" class="mb-2">Alamat</Label>
                        <Input id="address" placeholder="Alamat" />
                    </div>
                    <div class="mb-4">
                        <Label for="job" class="mb-2">Pekerjaan</Label>
                        <Input id="job" placeholder="Pekerjaan" />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="income" class="mb-2"
                            >Total Pendapatan Keluarga</Label
                        >
                        <Input
                            id="income"
                            placeholder="Total Pendapatan Keluarga"
                        />
                    </div>
                    <div class="mb-4">
                        <Label for="dependents" class="mb-2"
                            >Beban Tanggungan</Label
                        >
                        <Input id="dependents" placeholder="Beban Tanggungan" />
                    </div>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-center w-full">
                        <label
                            for="dropzone-file"
                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                        >
                            <div
                                class="flex flex-col items-center justify-center pt-5 pb-6"
                            >
                                <svg
                                    class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 20 16"
                                >
                                    <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                                    />
                                </svg>
                                <p
                                    class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-semibold"
                                >
                                    Upload Gambar
                                </p>
                            </div>
                            <input
                                id="dropzone-file"
                                type="file"
                                class="hidden"
                            />
                        </label>
                    </div>
                </div>
                <div class="block flex justify-end">
                    <Button type="submit" class="mr-3" color="red">Tolak</Button
                    >
                    <Button type="submit">Setujui</Button>
                </div>
            </form>
        </Modal>

        <div
            slot="footer"
            class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
            aria-label="Table navigation"
        >
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                Showing
                <span class="font-semibold text-gray-900 dark:text-white"
                    >{startRange}-{endRange}</span
                >
                of
                <span class="font-semibold text-gray-900 dark:text-white"
                    >{totalItems}</span
                >
            </span>
            <ButtonGroup>
                <Button
                    on:click={loadPreviousPage}
                    disabled={currentPosition === 0}
                    ><ChevronLeftOutline /></Button
                >
                {#each pagesToShow as pageNumber}
                    <Button on:click={() => goToPage(pageNumber)}
                        >{pageNumber}</Button
                    >
                {/each}
                <Button
                    on:click={loadNextPage}
                    disabled={totalPages === endPage}
                    ><ChevronRightOutline /></Button
                >
            </ButtonGroup>
        </div>
    </TableSearch>
</Layout> -->

<script lang="ts">
    import { onMount } from "svelte";
    import Layout from "./Layout.svelte";
    import {
        Badge,
        Table,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        TableSearch,
        Button,
        Modal,
        Label,
        Input,
        ButtonGroup,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        ImageOutline,
    } from "flowbite-svelte-icons";
    let items = [
        {
            id: 1,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            noHp: "081234567890",
            desc: "Surat Kependudukan",
            status: "Dalam Proses",
        },
    ];
    let modalDetailReqBansos = false;
    let searchTerm = "";
    let currentPosition = 0;
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;

    const updateDataAndPagination = () => {
        const currentPageItems = items.slice(
            currentPosition,
            currentPosition + itemsPerPage,
        );
        renderPagination(currentPageItems.length);
    };

    const loadNextPage = () => {
        if (currentPosition + itemsPerPage < items.length) {
            currentPosition += itemsPerPage;
            updateDataAndPagination();
        }
    };

    const loadPreviousPage = () => {
        if (currentPosition - itemsPerPage >= 0) {
            currentPosition -= itemsPerPage;
            updateDataAndPagination();
        }
    };

    const renderPagination = (totalItems: number) => {
        totalPages = Math.ceil(items.length / itemsPerPage);
        const currentPage = Math.ceil((currentPosition + 1) / itemsPerPage);

        startPage = currentPage - Math.floor(showPage / 2);
        startPage = Math.max(1, startPage);
        endPage = Math.min(startPage + showPage - 1, totalPages);

        pagesToShow = Array.from(
            { length: endPage - startPage + 1 },
            (_, i) => startPage + i,
        );
    };

    const goToPage = (pageNumber: number) => {
        currentPosition = (pageNumber - 1) * itemsPerPage;
        updateDataAndPagination();
    };

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    onMount(() => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);
    });

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item.name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );

    // SPK
    // SAW
    let alternatif = [
        { id: 1, nama: "Alternatif 1", kriteria: [70, 80, 90, 60, 50] },
        { id: 2, nama: "Alternatif 2", kriteria: [60, 85, 75, 70, 65] },
        { id: 3, nama: "Alternatif 3", kriteria: [75, 60, 80, 55, 70] },
        { id: 4, nama: "Alternatif 4", kriteria: [80, 70, 85, 65, 55] },
        { id: 5, nama: "Alternatif 5", kriteria: [65, 75, 70, 80, 60] },
        { id: 6, nama: "Alternatif 6", kriteria: [90, 65, 60, 75, 85] },
        { id: 7, nama: "Alternatif 7", kriteria: [85, 90, 65, 70, 75] },
        { id: 8, nama: "Alternatif 8", kriteria: [70, 85, 90, 60, 80] },
        { id: 9, nama: "Alternatif 9", kriteria: [60, 75, 85, 55, 90] },
        { id: 10, nama: "Alternatif 10", kriteria: [75, 80, 60, 70, 65] },
    ];
    let kriteriaBobot = [
        { nama: "Kriteria 1", bobot: 0.3, type: "cost" },
        { nama: "Kriteria 2", bobot: 0.25, type: "benefit" },
        { nama: "Kriteria 3", bobot: 0.15, type: "benefit" },
        { nama: "Kriteria 4", bobot: 0.1, type: "benefit" },
        { nama: "Kriteria 5", bobot: 0.2, type: "benefit" },
    ];

    let normalisasi = [];
    let hasilAkhir = [];

    // async function fetchData() {
    //     hitungNormalisasi();
    //     hitungNilaiAkhir();
    // }

    function hitungNormalisasi() {
        normalisasi = alternatif.map((alt) => {
            return {
                id: alt.id,
                nama: alt.nama,
                kriteria: alt.kriteria.map((nilai, index) => {
                    const max = Math.max(
                        ...alternatif.map((a) => a.kriteria[index]),
                    );
                    const min = Math.min(
                        ...alternatif.map((a) => a.kriteria[index]),
                    );
                    const type = kriteriaBobot[index].type;
                    return type === "benefit" ? nilai / max : min / nilai;
                }),
            };
        });
    }

    // Fungsi untuk menghitung nilai akhir
    function hitungNilaiAkhir() {
        hasilAkhir = normalisasi.map((alt) => {
            return {
                id: alt.id,
                nama: alt.nama,
                nilai: alt.kriteria.reduce(
                    (total, nilai, index) =>
                        total + nilai * kriteriaBobot[index].bobot,
                    0,
                ),
            };
        });
        hasilAkhir.sort((a, b) => b.nilai - a.nilai);
    }

    // // Fetch data saat komponen di-mount
    // onMount(fetchData);
    // End SAW

    // TOPSIS
    let normalisasiTopsis = [];
    let normalisasiBerbobot = [];
    let solusiIdealPositif = [];
    let solusiIdealNegatif = [];
    let jarakPositif = [];
    let jarakNegatif = [];
    let preferensi = [];

    async function fetchData() {
        hitungNormalisasi();
        hitungNilaiAkhir();
        hitungNormalisasiTopsis();
        hitungNormalisasiTopsisBerbobot();
        hitungSolusiIdeal();
        hitungJarak();
        hitungPreferensi();
        kombinasiHasilAkhir();
    }

    function hitungNormalisasiTopsis() {
        normalisasiTopsis = alternatif.map((alt) => {
            return {
                id: alt.id,
                nama: alt.nama,
                kriteria: alt.kriteria.map((nilai, index) => {
                    const sumOfSquares = Math.sqrt(
                        alternatif.reduce(
                            (sum, a) => sum + Math.pow(a.kriteria[index], 2),
                            0,
                        ),
                    );
                    return nilai / sumOfSquares;
                }),
            };
        });
    }

    function hitungNormalisasiTopsisBerbobot() {
        normalisasiBerbobot = normalisasiTopsis.map((alt) => {
            return {
                id: alt.id,
                nama: alt.nama,
                kriteria: alt.kriteria.map(
                    (nilai, index) => nilai * kriteriaBobot[index].bobot,
                ),
            };
        });
    }

    function hitungSolusiIdeal() {
        // solusiIdealPositif = kriteriaBobot.map((kriteria, index) => {
        //     return kriteria.type === "benefit"
        //         ? Math.max(...normalisasiBerbobot.map((a) => a.kriteria[index]))
        //         : Math.min(
        //               ...normalisasiBerbobot.map((a) => a.kriteria[index]),
        //           );
        // });

        // solusiIdealNegatif = kriteriaBobot.map((kriteria, index) => {
        //     return kriteria.type === "benefit"
        //         ? Math.min(...normalisasiBerbobot.map((a) => a.kriteria[index]))
        //         : Math.max(
        //               ...normalisasiBerbobot.map((a) => a.kriteria[index]),
        //           );
        // });
        solusiIdealPositif = kriteriaBobot.map((kriteria, index) => {
            return Math.max(
                ...normalisasiBerbobot.map((a) => a.kriteria[index]),
            );
        });

        solusiIdealNegatif = kriteriaBobot.map((kriteria, index) => {
            return Math.min(
                ...normalisasiBerbobot.map((a) => a.kriteria[index]),
            );
        });
    }

    function hitungJarak() {
        jarakPositif = normalisasiBerbobot.map((alt) => {
            return {
                id: alt.id,
                nama: alt.nama,
                jarak: Math.sqrt(
                    alt.kriteria.reduce(
                        (sum, nilai, index) =>
                            sum +
                            Math.pow(nilai - solusiIdealPositif[index], 2),
                        0,
                    ),
                ),
            };
        });

        jarakNegatif = normalisasiBerbobot.map((alt) => {
            return {
                id: alt.id,
                nama: alt.nama,
                jarak: Math.sqrt(
                    alt.kriteria.reduce(
                        (sum, nilai, index) =>
                            sum +
                            Math.pow(nilai - solusiIdealNegatif[index], 2),
                        0,
                    ),
                ),
            };
        });
    }

    function hitungPreferensi() {
        preferensi = jarakPositif.map((alt, index) => {
            return {
                id: alt.id,
                nama: alt.nama,
                nilai:
                    jarakNegatif[index].jarak /
                    (jarakNegatif[index].jarak + jarakPositif[index].jarak),
            };
        });

        preferensi.sort((a, b) => b.nilai - a.nilai);
    }

    let kombinasiHasil = [];
    const bobotSAW = 0.5; // Bobot untuk hasil SAW
    const bobotTOPSIS = 0.5; // Bobot untuk hasil TOPSIS

    function kombinasiHasilAkhir() {
        kombinasiHasil = hasilAkhir.map((sawAlt, index) => {
            const topsisAlt = preferensi.find(
                (prefAlt) => prefAlt.nama === sawAlt.nama,
            );
            return {
                id: sawAlt.id,
                nama: sawAlt.nama,
                nilai: sawAlt.nilai * bobotSAW + topsisAlt.nilai * bobotTOPSIS,
            };
        });

        kombinasiHasil.sort((a, b) => b.nilai - a.nilai);
    }

    onMount(fetchData);
    // End TOPSIS
</script>

<Layout>
    <h2 class="text-xl font-semibold mt-6 mb-2">Hasil Rekomendasi</h2>
    <Table>
        <TableHead>
            <TableHeadCell>Alternatif</TableHeadCell>
            <TableHeadCell>Nilai Perhitungan</TableHeadCell>
        </TableHead>
        <TableBody>
            {#each kombinasiHasil as komb}
                <TableBodyRow>
                    <TableBodyCell>{komb.id}</TableBodyCell>
                    <TableBodyCell>{komb.nama}</TableBodyCell>
                    <TableBodyCell>{komb.nilai.toFixed(4)}</TableBodyCell>
                </TableBodyRow>
            {/each}
        </TableBody>
    </Table>

    <!-- <h2 class="text-xl font-semibold mt-6 mb-2">Daftar Pengajuan Bansos</h2>
    <Table>
        <TableHead defaultRow={false}>
            <tr>
                <TableHeadCell rowspan="3">Alternatif</TableHeadCell>
                {#each kriteriaBobot as kriteria}
                    <TableHeadCell>{kriteria.nama}</TableHeadCell>
                {/each}
            </tr>
            <tr>
                {#each kriteriaBobot as kriteria}
                    <TableHeadCell>{kriteria.bobot}</TableHeadCell>
                {/each}
            </tr>
            <tr>
                {#each kriteriaBobot as kriteria}
                    <TableHeadCell>{kriteria.type}</TableHeadCell>
                {/each}
            </tr>
        </TableHead>
        <TableBody>
            {#each alternatif as alt}
                <TableBodyRow>
                    <TableBodyCell>{alt.nama}</TableBodyCell>
                    {#each alt.kriteria as nilai}
                        <TableBodyCell>{nilai}</TableBodyCell>
                    {/each}
                </TableBodyRow>
            {/each}
        </TableBody>
    </Table>
    <h2 class="text-xl font-semibold mt-6 mb-2">Hasil Rekomendasi</h2>
    <Table>
        <TableHead>
            <TableHeadCell>Alternatif</TableHeadCell>
            <TableHeadCell>Nilai Perhitungan</TableHeadCell>
        </TableHead>
        <TableBody>
            {#each kombinasiHasil as komb}
                <TableBodyRow>
                    <TableBodyCell>{komb.nama}</TableBodyCell>
                    <TableBodyCell>{komb.nilai.toFixed(4)}</TableBodyCell>
                </TableBodyRow>
            {/each}
        </TableBody>
    </Table>
    <hr />
    <h1 class="text-2xl font-bold mb-4">Sistem Pendukung Keputusan (SAW)</h1>

    <h2 class="text-xl font-semibold mt-6 mb-2">
        Data Alternatif dan Kriteria
    </h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Alternatif</TableBodyCell>
                {#each kriteriaBobot as kriteria}
                    <TableBodyCell>{kriteria.nama}</TableBodyCell>
                {/each}
            </TableBody>
        </TableHead>
        <TableBody>
            {#each alternatif as alt}
                <TableBody>
                    <TableBodyCell>{alt.nama}</TableBodyCell>
                    {#each alt.kriteria as nilai}
                        <TableBodyCell>{nilai}</TableBodyCell>
                    {/each}
                </TableBody>
            {/each}
        </TableBody>
    </Table>

    <h2 class="text-xl font-semibold mt-6 mb-2">Hasil Normalisasi</h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Alternatif</TableBodyCell>
                {#each kriteriaBobot as kriteria}
                    <TableBodyCell>{kriteria.nama}</TableBodyCell>
                {/each}
            </TableBody>
        </TableHead>
        <TableBody>
            {#each normalisasi as norm}
                <TableBody>
                    <TableBodyCell>{norm.nama}</TableBodyCell>
                    {#each norm.kriteria as nilai}
                        <TableBodyCell>{nilai.toFixed(2)}</TableBodyCell>
                    {/each}
                </TableBody>
            {/each}
        </TableBody>
    </Table>

    <h2 class="text-xl font-semibold mt-6 mb-2">Hasil Akhir</h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Alternatif</TableBodyCell>
                <TableBodyCell>Nilai Akhir</TableBodyCell>
            </TableBody>
        </TableHead>
        <TableBody>
            {#each hasilAkhir as hasil}
                <TableBody>
                    <TableBodyCell>{hasil.nama}</TableBodyCell>
                    <TableBodyCell>{hasil.nilai.toFixed(2)}</TableBodyCell>
                </TableBody>
            {/each}
        </TableBody>
    </Table>

    <h1 class="text-2xl font-bold mb-4">Sistem Pendukung Keputusan (TOPSIS)</h1>

    <h2 class="text-xl font-semibold mt-6 mb-2">
        Data Alternatif dan Kriteria
    </h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Alternatif</TableBodyCell>
                {#each kriteriaBobot as kriteria}
                    <TableBodyCell>{kriteria.nama}</TableBodyCell>
                {/each}
            </TableBody>
        </TableHead>
        <TableBody>
            {#each alternatif as alt}
                <TableBody>
                    <TableBodyCell>{alt.nama}</TableBodyCell>
                    {#each alt.kriteria as nilai}
                        <TableBodyCell>{nilai}</TableBodyCell>
                    {/each}
                </TableBody>
            {/each}
        </TableBody>
    </Table>

    <h2 class="text-xl font-semibold mt-6 mb-2">Matrik Normalisasi (R)</h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Alternatif</TableBodyCell>
                {#each kriteriaBobot as kriteria}
                    <TableBodyCell>{kriteria.nama}</TableBodyCell>
                {/each}
            </TableBody>
        </TableHead>
        <TableBody>
            {#each normalisasiTopsis as norm}
                <TableBody>
                    <TableBodyCell>{norm.nama}</TableBodyCell>
                    {#each norm.kriteria as nilai}
                        <TableBodyCell>{nilai.toFixed(4)}</TableBodyCell>
                    {/each}
                </TableBody>
            {/each}
        </TableBody>
    </Table>

    <h2 class="text-xl font-semibold mt-6 mb-2">
        Matrik Normalisasi Berbobot (Y)
    </h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Alternatif</TableBodyCell>
                {#each kriteriaBobot as kriteria}
                    <TableBodyCell>{kriteria.nama}</TableBodyCell>
                {/each}
            </TableBody>
        </TableHead>
        <TableBody>
            {#each normalisasiBerbobot as norm}
                <TableBody>
                    <TableBodyCell>{norm.nama}</TableBodyCell>
                    {#each norm.kriteria as nilai}
                        <TableBodyCell>{nilai.toFixed(4)}</TableBodyCell>
                    {/each}
                </TableBody>
            {/each}
        </TableBody>
    </Table>

    <h2 class="text-xl font-semibold mt-6 mb-2">
        Solusi Ideal Positif (A<sup>+</sup>) dan Solusi Ideal Negatif (A<sup
            >-</sup
        >)
    </h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Kriteria</TableBodyCell>
                {#each kriteriaBobot as kriteria}
                    <TableBodyCell>{kriteria.nama}</TableBodyCell>
                {/each}
            </TableBody>
        </TableHead>
        <TableBody>
            <TableBody>
                <TableBodyCell
                    >Solusi Ideal Positif (A<sup>+</sup>)</TableBodyCell
                >
                {#each solusiIdealPositif as nilai}
                    <TableBodyCell>{nilai.toFixed(4)}</TableBodyCell>
                {/each}
            </TableBody>
            <TableBody>
                <TableBodyCell
                    >Solusi Ideal Negatif (A<sup>-</sup>)</TableBodyCell
                >
                {#each solusiIdealNegatif as nilai}
                    <TableBodyCell>{nilai.toFixed(4)}</TableBodyCell>
                {/each}
            </TableBody>
        </TableBody>
    </Table>

    <h2 class="text-xl font-semibold mt-6 mb-2">
        Jarak Solusi Ideal Positif (D<sup>+</sup>) dan Jarak Solusi Ideal
        Negatif (D<sup>-</sup>)
    </h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Alternatif</TableBodyCell>
                <TableBodyCell>Jarak Positif (D<sup>+</sup>)</TableBodyCell>
                <TableBodyCell>Jarak Negatif (D<sup>-</sup>)</TableBodyCell>
            </TableBody>
        </TableHead>
        <TableBody>
            {#each jarakPositif as jarakP, index}
                <TableBody>
                    <TableBodyCell>{jarakP.nama}</TableBodyCell>
                    <TableBodyCell>{jarakP.jarak.toFixed(4)}</TableBodyCell>
                    <TableBodyCell
                        >{jarakNegatif[index].jarak.toFixed(4)}</TableBodyCell
                    >
                </TableBody>
            {/each}
        </TableBody>
    </Table>

    <h2 class="text-xl font-semibold mt-6 mb-2">Nilai Preferensi (V)</h2>
    <Table>
        <TableHead>
            <TableBody>
                <TableBodyCell>Alternatif</TableBodyCell>
                <TableBodyCell>Nilai Preferensi (V)</TableBodyCell>
            </TableBody>
        </TableHead>
        <TableBody>
            {#each preferensi as pref}
                <TableBody>
                    <TableBodyCell>{pref.nama}</TableBodyCell>
                    <TableBodyCell>{pref.nilai.toFixed(4)}</TableBodyCell>
                </TableBody>
            {/each}
        </TableBody>
    </Table>

    <h2 class="text-xl font-semibold mt-6 mb-2">Final answers</h2>
    <Table>
        <TableHead>
            <TableHeadCell>Alternatif</TableHeadCell>
            <TableHeadCell>Nilai Preferensi (V)</TableHeadCell>
        </TableHead>
        <TableBody>
            {#each kombinasiHasil as komb}
                <TableBodyRow>
                    <TableBodyCell>{komb.nama}</TableBodyCell>
                    <TableBodyCell>{komb.nilai.toFixed(4)}</TableBodyCell>
                </TableBodyRow>
            {/each}
        </TableBody>
    </Table> -->
</Layout>
