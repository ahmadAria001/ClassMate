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
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        ExclamationCircleOutline,
    } from "flowbite-svelte-icons";
    import Create from "./../Components/PendudukByRT/Modals/Create.svelte";
    import Edit from "./../Components/PendudukByRT/Modals/Edit.svelte";
    import Delete from "./../Components/PendudukByRT/Modals/Delete.svelte";

    import { page } from "@inertiajs/svelte";

    import axiosInstance from "axios";
    const axios = axiosInstance.create({ withCredentials: true });

    import { setCookie, getCookie } from "@R/Utils/Cokies";

    let items = [
        {
            id: 1,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            job: "Wirausaha",
            status: "Tetap",
        },
        {
            id: 2,
            name: "Yoga Saputra",
            address: "Jl. Mangga No. 12",
            job: "Wiraswasta",
            status: "Kontrak",
        },
        {
            id: 3,
            name: "Doni Prasojo",
            address: "Jl. Semangka No. 80",
            job: "Guru",
            status: "Kos",
        },
        // 459,
    ];
    let role = $page.props.auth.user.role;
    // "RT";
    let addCivilian = false;
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

    const title = "Lihat Data Warga";

    let selected: string | null = null;
    let selEdit: string | null = null;
    let selDel: string | null = null;

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

    const getData = async (
        id: string = "",
        landing: boolean = false,
        custom: {
            column: string;
            operator: string;
            value: string;
        } | null = null,
    ) => {
        // console.log($page.props.auth.user)
        const url = `/api/rt/${id}`;

        try {
            if (role === "RT") id = $page.props.auth.user.rt_id;

            if (!custom) {
                const response = await axios.get(url, {
                    headers: {
                        Accept: "*/*",
                    },
                });

                if (landing) {
                    let rawData = response.data;
                    let data = rawData.data[0].civils;
                    let landingData: any[] = [];

                    data.map((val: any) => {
                        if (val.isFamilyHead) landingData.push(val);
                    });

                    return landingData;
                }

                return response.data;
            }

            const response = await axios.get(
                `/api/civilian/${custom.column}/${custom.operator}/${custom.value}`,
            );
            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    onMount(async () => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);
    });

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item?.name?.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );

    const getWCV = async (id: string = "") => {
        const token = getCookie("token");
        const response = await axios.get(`/api/rt/cvl/${id}`, {
            headers: {
                Accept: "application/json",
            },
        });

        return response.data;
    };

    const getCivil = async (id: string = "") => {
        const token = getCookie("token");
        const response = await axios.get(`/api/civilian/${id}`, {
            headers: {
                Accept: "application/json",
            },
        });

        return response.data;
    };

    let custom: {
        column: string;
        operator: string;
        value: string;
    } = { column: "", operator: "", value: "" };
</script>

<svelte:head>
    <title>
        {title}
    </title>
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
                    addCivilian = true;
                }}>+ Tambah Warga</Button
            >
        </div>

        <Create bind:showState={addCivilian} />

        <TableHead>
            {#if role === "RT"}
                <TableHeadCell>Nama Kepala Keluarga</TableHeadCell>
                <TableHeadCell>Alamaat</TableHeadCell>
                <!-- <TableHeadCell>Pekerjaan</TableHeadCell> -->
                <TableHeadCell class="text-center" width="20%"
                    >Pekerjaan</TableHeadCell
                >
                <TableHeadCell class="text-center" width="20%"
                    >Status</TableHeadCell
                >
                <TableHeadCell class="sr-only"></TableHeadCell>
            {:else}
                <TableHeadCell>RT</TableHeadCell>
                <TableHeadCell>Ketua</TableHeadCell>
                <TableHeadCell></TableHeadCell>
                <!-- <TableHeadCell class="text-center" width="20%">Status</TableHeadCell -->
                <!-- > -->
                <!-- <TableHeadCell class="sr-only">Aksi</TableHeadCell> -->
            {/if}
        </TableHead>
        <TableBody>
            {#if role === "RT"}
                {#await getData("", true) then data}
                    <!-- {console.log(data.data[0])} -->
                    {#each data as item, idx}
                        <!-- {console.log(item)} -->

                        <TableBodyRow>
                            <TableBodyCell>{item.fullName}</TableBodyCell>
                            <TableBodyCell>{item.address}</TableBodyCell>
                            <!-- <TableBodyCell>{item.noHp}</TableBodyCell> -->
                            <TableBodyCell>{item.job}</TableBodyCell>
                            {#if item.residentstatus == "PermanentResident"}
                                <TableBodyCell class="text-center">
                                    <Badge color="green">Tetap</Badge>
                                </TableBodyCell>
                            {:else if item.residentstatus == "ContractResident"}
                                <TableBodyCell class="text-center">
                                    <Badge color="indigo">Kontrak</Badge>
                                </TableBodyCell>
                            {:else if item.residentstatus == "Kos"}
                                <TableBodyCell class="text-center">
                                    <Badge color="yellow">Kos</Badge>
                                </TableBodyCell>
                            {/if}

                            <TableBodyCell class="text-end">
                                <Button
                                    color="blue"
                                    on:click={() => {
                                        selected = item.id;
                                        custom.column = "nkk";
                                        custom.operator = "=";
                                        custom.value = item.nkk;

                                        modalFamily = true;
                                    }}>Detail</Button
                                >
                                <!-- tampilan edit keluarga? -->
                                <Button
                                    color="yellow"
                                    on:click={() => {
                                        selected = item.id;
                                        modalEdit = true;
                                    }}>Edit</Button
                                >
                                <Button
                                    color="red"
                                    on:click={() => {
                                        selDel = item.id;
                                        modalDelete = true;
                                    }}>Hapus</Button
                                >
                            </TableBodyCell>
                        </TableBodyRow>
                    {/each}
                {/await}
            {:else}
                {#await getData() then item}
                    {#each item.data as { id, leader_id, created_at, created_by, number, updated_at, updated_by, deleted_at, deleted_by }, idx}
                        <TableBodyRow>
                            <TableBodyCell>RT. {number}</TableBodyCell>
                            <TableBodyCell
                                >{leader_id
                                    ? leader_id.fullName
                                    : leader_id}</TableBodyCell
                            >
                            <!-- <TableBodyCell -->
                            <!--     >{new Date( -->
                            <!--         birthdate * 1000, -->
                            <!--     ).toLocaleDateString()}</TableBodyCell -->
                            <!-- > -->
                            <!-- {#if residentstatus == "PermanentResident"} -->
                            <!--     <TableBodyCell class="text-center"> -->
                            <!--         <Badge color="green">Tetap</Badge> -->
                            <!--     </TableBodyCell> -->
                            <!-- {:else if residentstatus == "ContractResident"} -->
                            <!--     <TableBodyCell class="text-center"> -->
                            <!--         <Badge color="indigo">Kontrak</Badge> -->
                            <!--     </TableBodyCell> -->
                            <!-- {:else if residentstatus == "Kos"} -->
                            <!--     <TableBodyCell class="text-center"> -->
                            <!--         <Badge color="yellow">Kos</Badge> -->
                            <!--     </TableBodyCell> -->
                            <!-- {/if} -->
                            <TableBodyCell class="text-end">
                                <!-- buttons base on role -->
                                {#if role == "RW"}
                                    <Button
                                        color="blue"
                                        on:click={() => {
                                            selected = id;
                                            modalFamily = true;
                                        }}>Detail</Button
                                    >
                                {/if}
                                {#if role == "RT"}
                                    <Button
                                        color="blue"
                                        on:click={() => {
                                            modalFamily = true;
                                        }}>Detail</Button
                                    >
                                    <!-- tampilan edit keluarga? -->
                                    <Button color="yellow">Edit</Button>
                                    <Button
                                        color="red"
                                        on:click={() => {
                                            selected = item.id;
                                            modalDelete = true;
                                        }}>Hapus</Button
                                    >
                                {/if}
                                {#if role == "Admin"}
                                    <Button
                                        color="blue"
                                        on:click={() => {
                                            selected = id;
                                            modalFamily = true;
                                        }}>Detail</Button
                                    >
                                    <Button
                                        color="yellow"
                                        on:click={() => {
                                            selected = item.id;
                                            modalEdit = true;
                                        }}>Edit</Button
                                    >
                                    <Button
                                        color="red"
                                        on:click={() => {
                                            selDel = item.id;
                                            modalDelete = true;
                                        }}>Hapus</Button
                                    >
                                {/if}
                            </TableBodyCell>
                        </TableBodyRow>
                    {/each}
                {/await}
            {/if}
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
                    <TableHeadCell>Nama Lengkap</TableHeadCell>
                    <TableHeadCell>Alamat</TableHeadCell>
                    <TableHeadCell>Pekerjaan</TableHeadCell>
                    <TableHeadCell class="text-center"
                        >Status Kependudukan</TableHeadCell
                    >
                    <TableHeadCell class="text-center">Kondisi</TableHeadCell>

                    {#if role == "RT" || role == "Admin"}
                        <TableHeadCell class="sr-only">Aksi</TableHeadCell>
                    {/if}
                </TableHead>
                <TableBody>
                    {#if selected}
                        {#if role == "RT"}
                            {#await getData("", false, custom) then data}
                                <!-- {console.log(data.data)} -->
                                {#each data.data as item, idx}
                                    <TableBodyRow>
                                        <TableBodyCell
                                            >{item.fullName}</TableBodyCell
                                        >
                                        <TableBodyCell
                                            >{item.address}</TableBodyCell
                                        >
                                        <TableBodyCell>{item.job}</TableBodyCell
                                        >
                                        {#if item.residentstatus == "PermanentResident"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="green"
                                                    >Tetap</Badge
                                                >
                                            </TableBodyCell>
                                        {:else if item.residentstatus == "ContractResident"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="indigo"
                                                    >Kontrak</Badge
                                                >
                                            </TableBodyCell>
                                        {:else if item.residentstatus == "Kos"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="yellow">Kos</Badge
                                                >
                                            </TableBodyCell>
                                        {/if}
                                        {#if item.status == "Aktif"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="green"
                                                    >Tetap</Badge
                                                >
                                            </TableBodyCell>
                                        {/if}
                                        {#if item.status == "Meninggal"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="red"
                                                    >{item.status}</Badge
                                                >
                                            </TableBodyCell>
                                        {:else if item.status == "Pindah"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="yellow"
                                                    >{item.status}</Badge
                                                >
                                            </TableBodyCell>
                                        {/if}

                                        <TableBodyCell>
                                            <Button
                                                color="yellow"
                                                on:click={() => {
                                                    modalEdit = true;
                                                }}>Edit</Button
                                            >
                                        </TableBodyCell>
                                    </TableBodyRow>
                                {/each}
                            {/await}
                        {:else}
                            {#await getWCV(selected) then data}
                                {#each data.data[0].civils as item, idx}
                                    <TableBodyRow>
                                        <TableBodyCell
                                            >{item.fullName}</TableBodyCell
                                        >
                                        <TableBodyCell
                                            >{item.address}</TableBodyCell
                                        >
                                        <TableBodyCell>{item.job}</TableBodyCell
                                        >
                                        {#if item.residentstatus == "PermanentResident"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="green"
                                                    >Tetap</Badge
                                                >
                                            </TableBodyCell>
                                        {:else if item.residentstatus == "ContractResident"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="indigo"
                                                    >Kontrak</Badge
                                                >
                                            </TableBodyCell>
                                        {:else if item.residentstatus == "Kos"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="yellow">Kos</Badge
                                                >
                                            </TableBodyCell>
                                        {/if}
                                        {#if item.status == "Aktif"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="green"
                                                    >Aktif</Badge
                                                >
                                            </TableBodyCell>
                                        {/if}
                                        {#if item.status == "Meninggal"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="red"
                                                    >{item.status}</Badge
                                                >
                                            </TableBodyCell>
                                        {/if}
                                        {#if item.status == "Pindah"}
                                            <TableBodyCell class="text-center">
                                                <Badge color="yellow"
                                                    >{item.status}</Badge
                                                >
                                            </TableBodyCell>
                                        {/if}

                                        {#if role == "RT"}
                                            <TableBodyCell>
                                                <Button
                                                    color="yellow"
                                                    on:click={() => {
                                                        modalEdit = true;
                                                    }}>Edit</Button
                                                >
                                            </TableBodyCell>
                                        {/if}
                                        {#if role === "Admin"}
                                            <TableBodyCell>
                                                <Button
                                                    color="yellow"
                                                    on:click={() => {
                                                        modalFamily = false;
                                                        selEdit = item.id;
                                                        modalEdit = true;
                                                    }}>Edit</Button
                                                >
                                                <Button
                                                    color="red"
                                                    on:click={() => {
                                                        selDel = item.id;
                                                        modalDelete = true;
                                                    }}>Hapus</Button
                                                >
                                            </TableBodyCell>
                                        {/if}
                                    </TableBodyRow>
                                {/each}
                            {/await}
                        {/if}
                    {/if}
                </TableBody>
            </Table>
        </Modal>

        <!-- modal edit -->
        {#if selEdit}
            {#await getCivil(selEdit) then data}
                <Edit bind:showState={modalEdit} {data} />
            {/await}
        {/if}

        <!-- modal hapus -->
        {#if selDel}
            {console.log(selDel)}
            {#await getCivil(selDel) then data}
                <Delete bind:showState={modalDelete} {data} />
            {/await}
        {/if}
        <!-- modal konfirmasi -->

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
