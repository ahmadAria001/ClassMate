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
        Button,
        Modal,
        Label,
        Input,
        ButtonGroup,
        Tabs,
        TabItem,
        Toast,
    } from "flowbite-svelte";
    import {
        CheckCircleSolid,
        ChevronLeftOutline,
        ChevronRightOutline,
        CloseCircleSolid,
        ImageOutline,
    } from "flowbite-svelte-icons";
    import TableSearch from "@C/General/TableSearch.svelte";
    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";

    const axios = axiosInstance.create({ withCredentials: true });
    const itemsPerPage = 10;
    const showPage = 5;
    const role = $page.props.auth.user.role;

    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };
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
    let calcModal = false;
    let searchTerm = "";
    let currentPosition = 0;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;
    let currentPage = 1;

    let data: any;

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
    let alternatif: any[] = [
        {
            id: 1,
            nama: "Alternatif 1",
            kriteria: [70, 80, 90, 60, 50],
            status: "Menunggu",
        },
        {
            id: 2,
            nama: "Alternatif 2",
            kriteria: [60, 85, 75, 70, 65],
            status: "Menunggu",
        },
        {
            id: 3,
            nama: "Alternatif 3",
            kriteria: [75, 60, 80, 55, 70],
            status: "Menunggu",
        },
        {
            id: 4,
            nama: "Alternatif 4",
            kriteria: [80, 70, 85, 65, 55],
            status: "Menunggu",
        },
        {
            id: 5,
            nama: "Alternatif 5",
            kriteria: [65, 75, 70, 80, 60],
            status: "Menunggu",
        },
        {
            id: 6,
            nama: "Alternatif 6",
            kriteria: [90, 65, 60, 75, 85],
            status: "Menunggu",
        },
        {
            id: 7,
            nama: "Alternatif 7",
            kriteria: [85, 90, 65, 70, 75],
            status: "Menunggu",
        },
        {
            id: 8,
            nama: "Alternatif 8",
            kriteria: [70, 85, 90, 60, 80],
            status: "Menunggu",
        },
        {
            id: 9,
            nama: "Alternatif 9",
            kriteria: [60, 75, 85, 55, 90],
            status: "Menunggu",
        },
        {
            id: 10,
            nama: "Alternatif 10",
            kriteria: [75, 80, 60, 70, 65],
            status: "Menunggu",
        },
    ];
    let kriteriaBobot = [
        { nama: "Pendapatan Bulanan", bobot: 0.3, type: "cost" },
        { nama: "Jumlah Tanggungan Anak", bobot: 0.25, type: "benefit" },
        { nama: "Status Pekerjaan", bobot: 0.15, type: "benefit" },
        { nama: "Status Tempat Tinggal", bobot: 0.1, type: "benefit" },
        { nama: "Pengeluaran Bulanan", bobot: 0.2, type: "benefit" },
    ];

    let normalisasi: any[] = [];
    let hasilAkhir: any[] = [];
    let sawNotSorted = [];

    // async function fetchData() {
    //     hitungNormalisasi();
    //     hitungNilaiAkhir();
    // }

    function hitungNormalisasi() {
        normalisasi = alternatif.map((alt) => {
            return {
                id: alt.id,
                nama: alt.nama,
                status: alt.status,
                kriteria: alt.kriteria.map((nilai: any, index: number) => {
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
                status: alt.status,
                nilai: alt.kriteria.reduce(
                    (total: any, nilai: any, index: number) =>
                        total + nilai * kriteriaBobot[index].bobot,
                    0,
                ),
            };
        });
        sawNotSorted = hasilAkhir;
        hasilAkhir.sort((a, b) => b.nilai - a.nilai);
    }

    // // Fetch data saat komponen di-mount
    // onMount(fetchData);
    // End SAW

    // TOPSIS
    let normalisasiTopsis: any[] = [];
    let normalisasiBerbobot: any[] = [];
    let solusiIdealPositif: any[] = [];
    let solusiIdealNegatif: any[] = [];
    let jarakPositif: any[] = [];
    let jarakNegatif: any[] = [];
    let preferensi: any[] = [];
    let topsisNotSorted = [];

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
                status: alt.status,
                kriteria: alt.kriteria.map((nilai: any, index: number) => {
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
                status: alt.status,
                kriteria: alt.kriteria.map(
                    (nilai: any, index: number) =>
                        nilai * kriteriaBobot[index].bobot,
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
                status: alt.status,
                jarak: Math.sqrt(
                    alt.kriteria.reduce(
                        (sum: any, nilai: any, index: number) =>
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
                status: alt.status,
                jarak: Math.sqrt(
                    alt.kriteria.reduce(
                        (sum: any, nilai: any, index: number) =>
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
                status: alt.status,
                nilai:
                    jarakNegatif.length > 1
                        ? jarakNegatif[index].jarak /
                          (jarakNegatif[index].jarak +
                              jarakPositif[index].jarak)
                        : 1,
            };
        });

        preferensi.sort((a, b) => b.nilai - a.nilai);
    }

    let kombinasiHasil: any = [];
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
                status: sawAlt.status,
                saw: sawAlt.nilai,
                topsis: topsisAlt.nilai,
                nilai: sawAlt.nilai * bobotSAW + topsisAlt.nilai * bobotTOPSIS,
            };
        });

        topsisNotSorted = kombinasiHasil;
        kombinasiHasil.sort((a: any, b: any) => b.nilai - a.nilai);
    }

    const getComplainPaged = async (page: number) => {
        let url = "";

        if (role == "Warga") return;

        if (role == "RT") url = `/api/bansos/rt/${encodeURIComponent(page)}`;
        if (role != "RT" && role != "Warga")
            url = `/api/bansos/p/${encodeURIComponent(page)}`;

        console.log(url);

        try {
            const response = await axios.get(url, {
                headers: {
                    Accept: "*/*",
                },
            });
            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const convertAlternative = (data: any[]) => {
        const converted: {
            id: number;
            nama: string;
            kriteria: number[];
            status: string;
        }[] = [];

        data.map((item: any, index: number) => {
            const criteria: number[] = [];
            criteria.push(item.salary);
            criteria.push(item.childrens);
            criteria.push(item.job_status);
            criteria.push(item.residence_status);
            criteria.push(item.expenses);

            converted.push({
                id: item.id,
                nama: item.request_by.civilian_id.fullName,
                kriteria: criteria,
                status: item.status,
            });
        });

        return converted;
    };

    const initData = async () => {
        data = await getComplainPaged(currentPage);
        alternatif = convertAlternative(data.data);
        // alternatif = data?.data;
        await fetchData(), (filteredData = kombinasiHasil);
    };

    let filteredData: any;
    onMount(async () => {
        await initData();
    });
    // End TOPSIS

    const handleSearch = async (event: any) => {
        const searchValue = event.detail.value.toLowerCase();
        console.log("Search value in handleSearch in use file:", searchValue);
        // filteredData = kombinasiHasil.filter((komb: any) =>
        //     komb.nama.toLowerCase().includes(searchValue),
        // );

        currentPage = 1;
        if (searchValue == "") {
            await initData();
            await rebuild();

            return;
        }

        data = await searchFA(searchValue);
        alternatif = convertAlternative(data.data);
        await fetchData(), (filteredData = kombinasiHasil);
        await rebuild();
    };
    let builder = {};

    const rebuild = async () => {
        builder = {};
    };

    const searchFA = async (filter: string = "") => {
        const response = await axios.get(
            `/api/bansos/like/${encodeURIComponent(currentPage)}/${encodeURIComponent(filter)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const handleSubmit = async (stat: number, id: number) => {
        try {
            let body = {
                id: id,
                status: stat,
                _token: $page.props.csrf_token,
            };

            if (stat == 3) {
                const response = await axios.delete("/api/bansos", {
                    data: {
                        id: id,
                        _token: $page.props.csrf_token,
                    },
                });

                err = response.data;
                await rebuild();

                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);

                return;
            }
            const response = await axios.put("/api/bansos", body);

            err = response.data;

            await rebuild();
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        } catch (error: any) {
            err = {
                message: error?.response?.data?.message,
                status: error?.response?.data?.status,
            };
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            console.error(error);

            if (error?.response?.status == 401) {
                err = {
                    message: "Anda tidak memiliki izin",
                    status: false,
                };
                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);
            }

            return;
        }
    };
</script>

<Layout>
    <!-- <h2 class="text-xl font-semibold mb-2 dark:text-white">
        Hasil Rekomendasi Pengajuan Bansos
    </h2> -->
    <TableSearch on:search={handleSearch}>
        <div slot="header">
            <Button color="blue" on:click={() => (calcModal = true)}
                >Detail Perhitungan</Button
            >
            <!-- <Button color="light">Pilih Cepat</Button> -->
        </div>
        <Table>
            <TableHead>
                <!-- <TableHeadCell>ID</TableHeadCell> -->
                <TableHeadCell>Alternatif</TableHeadCell>
                <TableHeadCell class="text-center">Status</TableHeadCell>
                <TableHeadCell class="text-center"
                    >Nilai Perhitungan</TableHeadCell
                >
                <TableHeadCell class="sr-only">Aksi</TableHeadCell>
            </TableHead>
            <TableBody>
                {#key builder}
                    {#if filteredData}
                        {#each filteredData as komb}
                            <TableBodyRow>
                                <!-- <TableBodyCell>{komb.id}</TableBodyCell> -->
                                <TableBodyCell>{komb.nama}</TableBodyCell>
                                {#if komb.status == 2}
                                    <TableBodyCell class="text-center"
                                        ><Badge color="yellow">Menunggu</Badge
                                        ></TableBodyCell
                                    >
                                {/if}
                                {#if komb.status == 1}
                                    <TableBodyCell class="text-center"
                                        ><Badge color="green">Menerima</Badge
                                        ></TableBodyCell
                                    >
                                {/if}
                                {#if komb.status == 0}
                                    <TableBodyCell class="text-center"
                                        ><Badge color="red"
                                            >Tidak Menerima</Badge
                                        ></TableBodyCell
                                    >
                                {/if}
                                <TableBodyCell class="text-center"
                                    >{komb.nilai.toFixed(4)}</TableBodyCell
                                >
                                <TableBodyCell class="text-end">
                                    {#if komb.status === 2}
                                        <Button
                                            color="green"
                                            on:click={async () =>
                                                await handleSubmit(1, komb.id)}
                                            >Terima</Button
                                        >
                                        <Button
                                            color="yellow"
                                            on:click={async () =>
                                                await handleSubmit(0, komb.id)}
                                            >Tolak</Button
                                        >
                                        <Button
                                            color="red"
                                            on:click={async () =>
                                                await handleSubmit(3, komb.id)}
                                            >Hapus</Button
                                        >
                                    {/if}
                                </TableBodyCell>
                            </TableBodyRow>
                        {/each}
                    {/if}
                {/key}
            </TableBody>
        </Table>
        <div
            slot="footer"
            class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
            aria-label="Table navigation"
        >
            {#if data}
                <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                >
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {currentPage < 2
                            ? 1
                            : data.length < 5 || data.data.length
                              ? data.length - data.data.length + 1
                              : data.data.length * currentPage - 5 + 1}
                        -
                        {data.length < 5
                            ? data.data.length
                            : data.data.length < 5
                              ? data.length
                              : data.data.length * currentPage}
                    </span>
                    of
                    <span class="font-semibold text-gray-900 dark:text-white"
                        >{data.length}</span
                    >
                </span>
                <!-- <ButtonGroup>
                    <Button
                        disabled={currentPage < 2}
                        on:click={async () => {
                            currentPage--;
                            await initData();
                        }}><ChevronLeftOutline /></Button
                    >
                    <Button disabled>{currentPage}</Button>
                    <Button
                        disabled={data.data.length < 5}
                        on:click={async () => {
                            currentPage++;
                            await initData();
                        }}><ChevronRightOutline /></Button
                    >
                </ButtonGroup> -->
            {/if}
        </div>
    </TableSearch>
</Layout>

<!-- modal perhitungan -->
<Modal
    title="Detail Perhitungan Pendukung Keputusan"
    bind:open={calcModal}
    size="lg"
>
    <Tabs tabStyle="underline">
        <TabItem open title="SAW">
            <h1 class="text-2xl font-bold mb-4 text-black dark:text-white">
                Metode Simple Additive Weighting(SAW)
            </h1>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Data Alternatif dan Kriteria
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    {#each kriteriaBobot as kriteria}
                        <TableHeadCell class="text-center"
                            >{kriteria.nama}</TableHeadCell
                        >
                    {/each}
                </TableHead>
                <TableBody>
                    {#each alternatif as alt}
                        <TableBodyRow>
                            <TableBodyCell>{alt.nama}</TableBodyCell>
                            {#each alt.kriteria as nilai}
                                <TableBodyCell class="text-center"
                                    >{nilai}</TableBodyCell
                                >
                            {/each}
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Hasil Normalisasi
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    {#each kriteriaBobot as kriteria}
                        <TableHeadCell>{kriteria.nama}</TableHeadCell>
                    {/each}
                </TableHead>
                <TableBody>
                    {#each normalisasi as norm}
                        <TableBodyRow>
                            <TableBodyCell>{norm.nama}</TableBodyCell>
                            {#each norm.kriteria as nilai}
                                <TableBodyCell>{nilai.toFixed(2)}</TableBodyCell
                                >
                            {/each}
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Hasil Akhir
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    <TableHeadCell>Nilai Akhir</TableHeadCell>
                </TableHead>
                <TableBody>
                    {#each hasilAkhir as hasil}
                        <TableBodyRow>
                            <TableBodyCell>{hasil.nama}</TableBodyCell>
                            <TableBodyCell
                                >{hasil.nilai.toFixed(2)}</TableBodyCell
                            >
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>
        </TabItem>
        <TabItem title="TOPSIS">
            <h1 class="text-2xl font-bold mb-4 text-black dark:text-white">
                Metode Technique for Order Preference by Similarity to Ideal
                Solution (TOPSIS)
            </h1>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Data Alternatif dan Kriteria
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    {#each kriteriaBobot as kriteria}
                        <TableHeadCell class="text-center"
                            >{kriteria.nama}</TableHeadCell
                        >
                    {/each}
                </TableHead>
                <TableBody>
                    {#each alternatif as alt}
                        <TableBodyRow>
                            <TableBodyCell>{alt.nama}</TableBodyCell>
                            {#each alt.kriteria as nilai}
                                <TableBodyCell class="text-center"
                                    >{nilai}</TableBodyCell
                                >
                            {/each}
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Matrik Normalisasi (R)
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    {#each kriteriaBobot as kriteria}
                        <TableHeadCell class="text-center"
                            >{kriteria.nama}</TableHeadCell
                        >
                    {/each}
                </TableHead>
                <TableBody>
                    {#each normalisasiTopsis as norm}
                        <TableBodyRow>
                            <TableBodyCell>{norm.nama}</TableBodyCell>
                            {#each norm.kriteria as nilai}
                                <TableBodyCell class="text-center"
                                    >{nilai.toFixed(4)}</TableBodyCell
                                >
                            {/each}
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Matrik Normalisasi Berbobot (Y)
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    {#each kriteriaBobot as kriteria}
                        <TableHeadCell class="text-center"
                            >{kriteria.nama}</TableHeadCell
                        >
                    {/each}
                </TableHead>
                <TableBody>
                    {#each normalisasiBerbobot as norm}
                        <TableBodyRow>
                            <TableBodyCell>{norm.nama}</TableBodyCell>
                            {#each norm.kriteria as nilai}
                                <TableBodyCell class="text-center"
                                    >{nilai.toFixed(4)}</TableBodyCell
                                >
                            {/each}
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Solusi Ideal Positif (A<sup>+</sup>) dan Solusi Ideal Negatif (A<sup
                    >-</sup
                >)
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Kriteria</TableHeadCell>
                    {#each kriteriaBobot as kriteria}
                        <TableHeadCell class="text-center"
                            >{kriteria.nama}</TableHeadCell
                        >
                    {/each}
                </TableHead>
                <TableBody>
                    <TableBodyRow>
                        <TableBodyCell class="text-center"
                            >Solusi Ideal Positif (A<sup>+</sup>)</TableBodyCell
                        >
                        {#each solusiIdealPositif as nilai}
                            <TableBodyCell class="text-center"
                                >{nilai.toFixed(4)}</TableBodyCell
                            >
                        {/each}
                    </TableBodyRow>
                    <TableBodyRow>
                        <TableBodyCell class="text-center"
                            >Solusi Ideal Negatif (A<sup>-</sup>)</TableBodyCell
                        >
                        {#each solusiIdealNegatif as nilai}
                            <TableBodyCell class="text-center"
                                >{nilai.toFixed(4)}</TableBodyCell
                            >
                        {/each}
                    </TableBodyRow>
                </TableBody>
            </Table>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Jarak Solusi Ideal Positif (D<sup>+</sup>) dan Jarak Solusi
                Ideal Negatif (D<sup>-</sup>)
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    <TableHeadCell>Jarak Positif (D<sup>+</sup>)</TableHeadCell>
                    <TableHeadCell>Jarak Negatif (D<sup>-</sup>)</TableHeadCell>
                </TableHead>
                <TableBody>
                    {#each jarakPositif as jarakP, index}
                        <TableBodyRow>
                            <TableBodyCell>{jarakP.nama}</TableBodyCell>
                            <TableBodyCell
                                >{jarakP.jarak.toFixed(4)}</TableBodyCell
                            >
                            <TableBodyCell
                                >{jarakNegatif[index].jarak.toFixed(
                                    4,
                                )}</TableBodyCell
                            >
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>

            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Nilai Preferensi (V)
            </h2>
            <Table>
                <TableHead>
                    <TableBodyCell>Alternatif</TableBodyCell>
                    <TableBodyCell>Nilai Preferensi (V)</TableBodyCell>
                </TableHead>
                <TableBody>
                    {#each preferensi as pref}
                        <TableBodyRow>
                            <TableBodyCell>{pref.nama}</TableBodyCell>
                            <TableBodyCell
                                >{pref.nilai.toFixed(4)}</TableBodyCell
                            >
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>

            <!-- <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Ranking
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    <TableHeadCell class="text-center"
                        >Nilai Preferensi (V)</TableHeadCell
                    >
                </TableHead>
                <TableBody>
                    {#each preferensi as pref, idx}
                        <TableBodyRow>
                            <TableBodyCell>{pref.nama}</TableBodyCell>
                            <TableBodyCell class="text-center">
                                {idx + 1}</TableBodyCell
                            >
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table> -->
        </TabItem>
        <TabItem title="Kombinasi Perhitungan">
            <h2
                class="text-xl font-semibold mt-6 mb-2 text-black dark:text-white"
            >
                Rangking hasil Kombinasi
            </h2>
            <Table>
                <TableHead>
                    <TableHeadCell>Alternatif</TableHeadCell>
                    <TableHeadCell>SAW</TableHeadCell>
                    <TableHeadCell>TOPSIS</TableHeadCell>
                    <TableHeadCell>Hasil</TableHeadCell>
                </TableHead>
                <TableBody>
                    {#each kombinasiHasil as komb}
                        <TableBodyRow>
                            <TableBodyCell>{komb.nama}</TableBodyCell>

                            <TableBodyCell>
                                {komb.saw.toFixed(4)}
                            </TableBodyCell>
                            <TableBodyCell>
                                {komb.topsis.toFixed(4)}
                            </TableBodyCell>
                            <TableBodyCell>
                                {komb.nilai.toFixed(4)}
                            </TableBodyCell>
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>
        </TabItem>
    </Tabs>
</Modal>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
