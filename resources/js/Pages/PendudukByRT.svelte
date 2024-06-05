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
        Popover,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        ExclamationCircleOutline,
        TerminalSolid,
        QuestionCircleSolid,
    } from "flowbite-svelte-icons";
    import Create from "./../Components/PendudukByRT/Modals/Create.svelte";
    import Edit from "./../Components/PendudukByRT/Modals/Edit.svelte";
    import Delete from "./../Components/PendudukByRT/Modals/Delete.svelte";

    import { page } from "@inertiajs/svelte";

    import axiosInstance from "axios";
    const axios = axiosInstance.create({ withCredentials: true });

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

    let data: any;
    let role = $page.props.auth.user.role;
    // let role = "RT";
    // "RT";
    let addCivilian = false;
    let modalEdit = false;
    let modalFamily = false;
    let modalDelete = false;

    let searchTerm = "";
    let currentPosition = 0;
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;

    let currentPage = 1;

    const title = "Lihat Data Warga";

    let selected: string | null = null;
    let selEdit: string | null = null;
    let selDel: string | null = null;

    let builder = {};

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
        try {
            if (role == "RT") id = $page.props.auth.user.rt_id;
            // await getDataByRT(id,landing)

            let url = `/api/rt/${currentPage}/${id}`;

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

                    let lastNKK: string | null = null;

                    if (landingData.length < 1) {
                        data.map((val: any) => {
                            if (lastNKK) {
                                if (lastNKK !== val.nkk) landingData.push(val);
                            }

                            if (!lastNKK) {
                                lastNKK = val.nkk;
                                landingData.push(val);
                            }
                        });
                    }

                    return { data: landingData, length: landingData.length };
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

    const initPage = async () => {
        if (role == "RT") {
            data = await getData("", true);
        } else {
            data = await getData();
        }
    };

    onMount(async () => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);
        await initPage();
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
        const response = await axios.get(
            `/api/rt/cvl/0/${encodeURIComponent(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const getCivil = async (id: string = "") => {
        const response = await axios.get(`/api/civilian/${id}`, {
            headers: {
                Accept: "application/json",
            },
        });

        return response.data;
    };

    export const rebuild = async () => {
        await initPage();

        builder = {};
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
        class="md:w-auto flex flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
        {#if role === "RT"}
        <Button
            on:click={() => {
                addCivilian = true;
            }}>+ Tambah Warga</Button
        >
        {/if}
    </div>       
        

        <Create bind:showState={addCivilian} on:comp={rebuild} />

        <TableHead>
            {#if role === "RT"}
                <TableHeadCell width="25%">Nama Kepala Keluarga</TableHeadCell>
                <TableHeadCell>Alamat</TableHeadCell>
                <!-- <TableHeadCell>Pekerjaan</TableHeadCell> -->
                <TableHeadCell width="10%">Pekerjaan</TableHeadCell>
                <TableHeadCell class="text-center" width="20%"
                    >Status Kependudukan</TableHeadCell
                >
                <TableHeadCell class="text-center"
                    >Status Penduduk</TableHeadCell
                >
                <TableHeadCell class="sr-only"></TableHeadCell>
            {:else}
                <TableHeadCell>RT</TableHeadCell>
                <TableHeadCell>Ketua</TableHeadCell>
                <TableHeadCell></TableHeadCell>
            {/if}
        </TableHead>
        <TableBody>
            {#key builder}
                {#if data}
                    {#if role === "RT"}
                        {#each data.data as item, idx}
                            <TableBodyRow>
                                <TableBodyCell class="max-w-xs truncate"
                                    >{item.fullName}</TableBodyCell
                                >
                                <TableBodyCell>{item.address}</TableBodyCell>
                                <TableBodyCell class="text-left"
                                    >{item.job}</TableBodyCell
                                >
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
                                    <Button
                                        color="yellow"
                                        on:click={() => {
                                            modalEdit = true;
                                            selEdit = item.id;
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
                    {:else}
                        {#each data.data as { id, leader_id, created_at, created_by, number, updated_at, updated_by, deleted_at, deleted_by }, idx}
                            <TableBodyRow>
                                <TableBodyCell>RT. {number}</TableBodyCell>
                                <TableBodyCell
                                    >{leader_id
                                        ? leader_id.civilian_id.fullName
                                        : leader_id}</TableBodyCell
                                >
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
                                        <Button color="yellow">Edit</Button>
                                        <Button
                                            color="red"
                                            on:click={() => {
                                                selected = id;
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
                                    {/if}
                                </TableBodyCell>
                            </TableBodyRow>
                        {/each}
                    {/if}
                {/if}
            {/key}
        </TableBody>

        <!-- modal detail -->
        {#key builder}
            <Modal
                title="Data Keluarga"
                bind:open={modalFamily}
                size="xl"
                autoclose
            >
                <Table>
                    <TableHead>
                        <TableHeadCell width="20%">Nama Lengkap</TableHeadCell>
                        <TableHeadCell>Alamat</TableHeadCell>
                        <TableHeadCell>Pekerjaan</TableHeadCell>
                        <TableHeadCell class="text-center"
                            >Status Kependudukan</TableHeadCell
                        >

                        {#if role == "RT" || role == "Admin"}
                            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
                        {/if}
                    </TableHead>
                    <TableBody>
                        {#if selected}
                            {#if role == "RT"}
                                {#await getData("", false, custom) then data}
                                    {#each data.data as item, idx}
                                        <TableBodyRow>
                                            <TableBodyCell
                                                class="max-w-xs truncate"
                                            >
                                                {item.fullName}
                                            </TableBodyCell>
                                            <TableBodyCell
                                                >{item.address}</TableBodyCell
                                            >
                                            <TableBodyCell
                                                >{item.job}</TableBodyCell
                                            >
                                            {#if item.residentstatus == "PermanentResident"}
                                                <TableBodyCell
                                                    class="text-center"
                                                >
                                                    <Badge color="green"
                                                        >Tetap</Badge
                                                    >
                                                </TableBodyCell>
                                            {:else if item.residentstatus == "ContractResident"}
                                                <TableBodyCell
                                                    class="text-center"
                                                >
                                                    <Badge color="indigo"
                                                        >Kontrak</Badge
                                                    >
                                                </TableBodyCell>
                                            {:else if item.residentstatus == "Kos"}
                                                <TableBodyCell
                                                    class="text-center"
                                                >
                                                    <Badge color="yellow"
                                                        >Kos</Badge
                                                    >
                                                </TableBodyCell>
                                            {/if}

                                            <TableBodyCell>
                                                <Button
                                                    color="yellow"
                                                    on:click={() => {
                                                        modalEdit = true;
                                                        selEdit = item.id;
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
                                {#await getWCV(selected) then data}
                                    {#each data.data[0].civils as item, idx}
                                        <TableBodyRow>
                                            <TableBodyCell
                                                tdClass="flex gap-2 my-6 {(!item.isFamilyHead)? 'pl-7':''}"
                                            >
                                                {#if item.isFamilyHead}
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="w-5 h-5 fill-white"
                                                        viewBox="0 0 24 24"
                                                        ><path
                                                            d="m21 2-5 5-4-5-4 5-5-5v13h18zM5 21h14a2 2 0 0 0 2-2v-2H3v2a2 2 0 0 0 2 2z"
                                                        ></path></svg
                                                    >
                                                {/if}

                                                {item.fullName}
                                            </TableBodyCell>
                                            <Popover
                                                class="w-64 text-sm text-white"
                                                title="Alamat"
                                                triggeredBy={`#address-${item.id}`}
                                            >
                                                {item.address}
                                            </Popover>
                                            <TableBodyCell>
                                                <div
                                                    class="flex justify-between align-middle gap-2"
                                                >
                                                    <span
                                                        class="w-1/2 truncate"
                                                    >
                                                        {item.address}
                                                    </span>
                                                    <QuestionCircleSolid
                                                        id={`address-${item.id}`}
                                                    />
                                                </div>
                                            </TableBodyCell>
                                            <TableBodyCell
                                                >{item.job}</TableBodyCell
                                            >
                                            {#if item.residentstatus == "PermanentResident"}
                                                <TableBodyCell
                                                    class="text-center"
                                                >
                                                    <Badge color="green"
                                                        >Tetap</Badge
                                                    >
                                                </TableBodyCell>
                                            {:else if item.residentstatus == "ContractResident"}
                                                <TableBodyCell
                                                    class="text-center"
                                                >
                                                    <Badge color="indigo"
                                                        >Kontrak</Badge
                                                    >
                                                </TableBodyCell>
                                            {:else if item.residentstatus == "Kos"}
                                                <TableBodyCell
                                                    class="text-center"
                                                >
                                                    <Badge color="yellow"
                                                        >Kos</Badge
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
                    <Edit bind:showState={modalEdit} {data} on:comp={rebuild} />
                {/await}
            {/if}

            <!-- modal hapus -->
            {#if selDel}
                {#await getCivil(selDel) then data}
                    <Delete
                        bind:showState={modalDelete}
                        {data}
                        on:comp={rebuild}
                    />
                {/await}
            {/if}
        {/key}
        <!-- modal konfirmasi -->

        <div
            slot="footer"
            class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
            aria-label="Table navigation"
        >
            {#if data && role != "RT"}
                <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                >
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {currentPage < 2
                            ? 1
                            : data.data.length < 6
                              ? data.length - data.data.length + 1
                              : data.data.length + 1}
                        -
                        {data.data.length < 6
                            ? data.length
                            : data.data.length * currentPage}
                    </span>
                    of
                    <span class="font-semibold text-gray-900 dark:text-white"
                        >{data.length}</span
                    >
                </span>
                <ButtonGroup>
                    <Button
                        disabled={currentPage < 2}
                        on:click={async () => {
                            currentPage--;
                            await initPage();
                        }}><ChevronLeftOutline /></Button
                    >
                    <!-- {#each data.length as pageNumber} -->
                    <Button disabled>{currentPage}</Button>
                    <!-- {/each} -->
                    <Button
                        disabled={currentPage >= data.length / 5}
                        on:click={async () => {
                            currentPage++;
                            await initPage();
                        }}><ChevronRightOutline /></Button
                    >
                </ButtonGroup>
            {/if}
        </div>
    </TableSearch>
</Layout>
