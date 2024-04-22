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
        type SizeType,
        Select,
        Avatar,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        ExclamationCircleOutline,
    } from "flowbite-svelte-icons";
    let items = [
        {
            id: 1,
            name: "Muhammad Fatoni",
            src: "https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg",
            head: "1",
            address: "Jl. Semangka No. 80",
            job: "Wirausaha",
            status: "Tetap",
        },
        {
            id: 2,
            name: "Yoga Saputra",
            src: "https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg",
            head: "2",
            address: "Jl. Mangga No. 12",
            job: "Wiraswasta",
            status: "Kontrak",
        },
        {
            id: 3,
            name: "Doni Prasojo",
            src: "https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg",
            head: "3",
            address: "Jl. Semangka No. 80",
            job: "Guru",
            status: "Kos",
        },
    ];
    let addRT = false;
    let modalEdit = false;
    let modalFamily = false;
    let modalDelete = false;
    let modalConfirmDel = false;
    let selectedReason: string;
    let delReasons = [
        { value: "pindah", name: "Pindah" },
        { value: "meninggal", name: "Meninggal" },
    ];

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

    const title = "Lihat Data Warga";
</script>

<svelte:head>
    {title}
</svelte:head>

<Layout active={title}>
    <TableSearch
        placeholder="Cari warga"
        hoverable={true}
        bind:inputValue={searchTerm}
        divClass="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden"
        innerDivClass="flex items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        classInput="text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2  pl-10"
    >
        <div
            slot="header"
            class="md:w-auto flex flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
        >
            <Button
                on:click={() => {
                    addRT = true;
                }}>+ Tambah RT</Button
            >
        </div>
        <Modal title="Tambah RT" bind:open={addRT} autoclose>
            <form method="POST">
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="full_name" class="mb-2">Nama Lengkap</Label>
                        <Input id="full_name" placeholder="Nama Lengkap" />
                    </div>
                    <div class="mb-4">
                        <Label for="address" class="mb-2">Alamat</Label>
                        <Input id="address" placeholder="Alamat" />
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
                        <Label for="religion" class="mb-2">Agama</Label>
                        <Input id="religion" placeholder="Agama" />
                    </div>
                    <div class="mb-4">
                        <Label for="birthdate" class="mb-2"
                            >Tempat, Tanggal Lahir</Label
                        >
                        <Input
                            type="date"
                            id="birthdate"
                            placeholder="Tempat, Tanggal Lahir"
                        />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4 md:order-last">
                        <div class="mb-4">
                            <Label for="chief" class="mb-2">Ketua RT</Label>
                            <Input id="chief" placeholder="Ketua RT" />
                        </div>
                        <div class="mb-4">
                            <Label for="status" class="mb-2">Status</Label>
                            <Input id="status" placeholder="Status" />
                        </div>
                        <div class="mb-4">
                            <Label for="noHp" class="mb-2">No. HP</Label>
                            <Input id="noHp" placeholder="No. HP" />
                        </div>
                        <div class="mb-4">
                            <Label for="job" class="mb-2">Pekerjaan</Label>
                            <Input id="job" placeholder="Pekerjaan" />
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            for="dropzone-file"
                            class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
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
                <div class="block flex">
                    <Button type="submit" class="ml-auto">Simpan</Button>
                </div>
            </form>
        </Modal>

        <TableHead>
            <TableHeadCell>Nama Lengkap</TableHeadCell>
            <TableHeadCell width="20%">Ketua RT</TableHeadCell>
            <TableHeadCell width="30%">Alamat RT</TableHeadCell>
            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
        </TableHead>
        <TableBody>
            {#each filteredItems as item}
                <TableBodyRow>
                    <TableBodyCell class="flex items-center">
                        <Avatar src={item.src} class="mr-3" />
                        {item.name}
                    </TableBodyCell>
                    <TableBodyCell>RT {item.head}</TableBodyCell>
                    <TableBodyCell>{item.address}</TableBodyCell>
                    <TableBodyCell>
                        <Button href="/warga-rt">Lihat Penduduk</Button>
                        <Button
                            color="yellow"
                            on:click={() => {
                                modalEdit = true;
                            }}>Edit</Button
                        >
                        <Button color="red">Hapus</Button>
                    </TableBodyCell>
                </TableBodyRow>
            {/each}
        </TableBody>

        <!-- modal detail -->
        <Modal
            title="Data Keluarga"
            bind:open={modalFamily}
            size="lg"
            autoclose
        >
            <Table>
                <TableHead>
                    <TableHeadCell>Nama</TableHeadCell>
                    <TableHeadCell>Ketua RT</TableHeadCell>
                    <TableHeadCell>Alamat RT</TableHeadCell>
                    <TableHeadCell class="sr-only">Aksi</TableHeadCell>
                </TableHead>
                <TableBody>
                    {#each filteredItems as item}
                        <TableBodyRow>
                            <TableBodyCell>{item.name}</TableBodyCell>
                            <TableBodyCell>{item.address}</TableBodyCell>
                            <TableBodyCell>{item.job}</TableBodyCell>
                            <TableBodyCell>
                                <Button
                                    color="yellow"
                                    on:click={() => {
                                        modalEdit = true;
                                    }}>Edit</Button
                                >
                                <Button>Hapus</Button>
                            </TableBodyCell>
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>
        </Modal>

        <!-- modal edit -->
        <Modal title="Tambah RT" bind:open={modalEdit} autoclose>
            <form method="POST">
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="full_name" class="mb-2">Nama Lengkap</Label>
                        <Input id="full_name" placeholder="Nama Lengkap" />
                    </div>
                    <div class="mb-4">
                        <Label for="address" class="mb-2">Alamat</Label>
                        <Input id="address" placeholder="Alamat" />
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
                        <Label for="religion" class="mb-2">Agama</Label>
                        <Input id="religion" placeholder="Agama" />
                    </div>
                    <div class="mb-4">
                        <Label for="birthdate" class="mb-2"
                            >Tempat, Tanggal Lahir</Label
                        >
                        <Input
                            type="date"
                            id="birthdate"
                            placeholder="Tempat, Tanggal Lahir"
                        />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4 md:order-last">
                        <div class="mb-4">
                            <Label for="chief" class="mb-2">Ketua RT</Label>
                            <Input id="chief" placeholder="Ketua RT" />
                        </div>
                        <div class="mb-4">
                            <Label for="status" class="mb-2">Status</Label>
                            <Input id="status" placeholder="Status" />
                        </div>
                        <div class="mb-4">
                            <Label for="noHp" class="mb-2">No. HP</Label>
                            <Input id="noHp" placeholder="No. HP" />
                        </div>
                        <div class="mb-4">
                            <Label for="job" class="mb-2">Pekerjaan</Label>
                            <Input id="job" placeholder="Pekerjaan" />
                        </div>
                    </div>
                    <div class="mb-4">
                        <label
                            for="dropzone-file"
                            class="flex flex-col items-center justify-center w-full h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
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
                <div class="block flex">
                    <Button type="submit" class="ml-auto">Simpan</Button>
                </div>
            </form>
        </Modal>

        <!-- modal hapus -->
        <Modal bind:open={modalDelete} size="sm" autoclose>
            <div class="text-center">
                <ExclamationCircleOutline
                    class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
                />
                <h3
                    class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                >
                    Apa alasan data warga 'nama' dihapus?
                </h3>
                <Select
                    class="my-2"
                    items={delReasons}
                    bind:value={selectedReason}
                    placeholder="Alasan dihapus"
                    size="sm"
                />
                <Button
                    color="red"
                    class="me-2"
                    on:click={() => {
                        modalConfirmDel = true;
                    }}>Selanjutnya</Button
                >
                <Button color="alternative">Batal</Button>
            </div>
        </Modal>

        <!-- modal konfirmasi -->
        <Modal bind:open={modalConfirmDel} size="sm" autoclose>
            <div class="text-center">
                <ExclamationCircleOutline
                    class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
                />
                <h3
                    class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                >
                    Apakah yakin ingin menghapus warga ini?
                </h3>
                <Button color="red" class="me-2">Ya, yakin</Button>
                <Button color="alternative">Tidak, batal</Button>
            </div>
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
</Layout>
