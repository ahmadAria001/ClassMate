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
        Popover,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        ImageOutline,
        QuestionCircleSolid,
    } from "flowbite-svelte-icons";

    import axiosInstance from "axios";
    import Edit from "@C/Pengaduan/Modals/Detail.svelte";

    const axios = axiosInstance.create({ withCredentials: true });

    let items = [
        {
            id: 1,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            noHp: "081234567890",
            problem: "Saluran Pembuangan Macet",
            status: "Selesai",
        },
        {
            id: 2,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            noHp: "081234567890",
            problem: "Pos Ronda Rusak",
            status: "Dalam Proses",
        },
    ];
    let modalDetailPengaduan = false;
    let searchTerm = "";
    let currentPosition = 0;
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;
    let selected: string | null = null;

    let builder = {};

    const rebuild = () => {
        builder = {};
    };

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

    const getComplaints = async (id: string = "") => {
        try {
            const response = await axios.get(`/api/docs/complaint/${id}`, {
                headers: {
                    Accept: "*/*",
                },
            });
            return response.data;
        } catch (error) {
            console.error(error);
        }
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
            item.problem.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );
</script>

<Layout>
    <TableSearch
        placeholder="Cari Pengaduan"
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
            <TableHeadCell>Permasalahan</TableHeadCell>
            <TableHeadCell class="text-center">Status</TableHeadCell>
            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
        </TableHead>
        <TableBody>
            {#key builder}
                {#await getComplaints() then data}
                    {#each data.data as item}
                        <TableBodyRow>
                            <TableBodyCell>
                                <span
                                    class="w-1/4 truncate
"
                                >
                                    {item.created_by.civilian_id.fullName}
                                </span>
                            </TableBodyCell>
                            <TableBodyCell tdClass="max-w-52">
                                <div
                                    class="flex justify-between align-middle gap-2"
                                >
                                    <span class="w-full truncate">
                                        {item.created_by.civilian_id.address}
                                    </span>
                                    <QuestionCircleSolid
                                        id={`address-${item.id}`}
                                    />
                                </div>
                            </TableBodyCell>
                            <TableBodyCell
                                >{item.created_by.civilian_id
                                    .phone}</TableBodyCell
                            >
                            <TableBodyCell class="w-20">
                                <div
                                    class="flex justify-between max-w-52 align-middle gap-2"
                                >
                                    <span class="w-full truncate">
                                        {item.docs_id.description}
                                    </span>
                                    <QuestionCircleSolid
                                        id={`desc-${item.id}`}
                                    />
                                </div>
                            </TableBodyCell>
                            {#if item.complaintStatus == "Resolved"}
                                <TableBodyCell class="text-center">
                                    <Badge color="green"
                                        >{item.complaintStatus}</Badge
                                    >
                                </TableBodyCell>
                            {:else if item.complaintStatus == "Open"}
                                <TableBodyCell class="text-center">
                                    <Badge color="primary"
                                        >{item.complaintStatus}</Badge
                                    >
                                </TableBodyCell>
                            {:else}
                                <TableBodyCell class="text-center">
                                    <Badge color="red"
                                        >{item.complaintStatus}</Badge
                                    >
                                </TableBodyCell>
                            {/if}

                            <TableBodyCell>
                                <Button
                                    color="blue"
                                    on:click={() => {
                                        selected = item.id;
                                        modalDetailPengaduan = true;
                                    }}>Detail</Button
                                >
                            </TableBodyCell>
                        </TableBodyRow>

                        <Popover
                            class="w-64 text-sm text-black dark:text-white"
                            title="Alamat"
                            triggeredBy={`#address-${item.id}`}
                        >
                            {item.created_by.civilian_id.address}
                        </Popover>

                        <Popover
                            class="w-64 text-sm text-black dark:text-white"
                            title="Masalah"
                            triggeredBy={`#desc-${item.id}`}
                        >
                            {item.docs_id.description}
                        </Popover>
                    {/each}
                {/await}
            {/key}
        </TableBody>

        <!-- <Modal -->
        <!--     title="Deskripsi Pengaduan" -->
        <!--     bind:open={modalDetailPengaduan} -->
        <!--     autoclose -->
        <!-- > -->
        <!--     <form method="POST"> -->
        <!--         <div class="mb-4"> -->
        <!--             <Label for="full_name" class="mb-2">Nama Pelapor</Label> -->
        <!--             <Input id="full_name" placeholder="Nama Pelapor" /> -->
        <!--         </div> -->
        <!--         <!-- <div class="grid md:grid-cols-2 md:gap-6"> -->
        <!--             <div class="mb-4"> -->
        <!--                 <Label for="no_hp" class="mb-2">No HP</Label> -->
        <!--                 <Input id="no_hp" placeholder="No HP" /> -->
        <!--             </div> -->
        <!--             <div class="mb-4"> -->
        <!--                 <Label for="address" class="mb-2">Alamat</Label> -->
        <!--                 <Input id="address" placeholder="Alamat" /> -->
        <!--             </div> -->
        <!--         </div> -->
        <!--         <div class="mb-4"> -->
        <!--             <Label for="no_hp" class="mb-2">No HP</Label> -->
        <!--             <Input id="no_hp" placeholder="No HP" /> -->
        <!--         </div> -->
        <!--         <div class="mb-4"> -->
        <!--             <Label for="address" class="mb-2">Alamat</Label> -->
        <!--             <Input id="address" placeholder="Alamat" /> -->
        <!--         </div> -->
        <!--         <div class="mb-4"> -->
        <!--             <Label for="timeUpload" class="mb-2">Waktu Dikirim</Label> -->
        <!--             <Input id="timeUpload" placeholder="Waktu Dikirim" /> -->
        <!--         </div> -->
        <!--         <div class="mb-4"> -->
        <!--             <Label for="problems" class="mb-2">Permasalahan</Label> -->
        <!--             <Input id="problems" placeholder="Permasalahan" /> -->
        <!--         </div> -->
        <!--         <div class="mb-4"> -->
        <!--             <div class="flex items-center justify-center w-full"> -->
        <!--                 <label -->
        <!--                     for="dropzone-file" -->
        <!--                     class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600" -->
        <!--                 > -->
        <!--                     <div -->
        <!--                         class="flex flex-col items-center justify-center pt-5 pb-6" -->
        <!--                     > -->
        <!--                         <ImageOutline size="xl" /> -->
        <!--                         <p -->
        <!--                             class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-semibold" -->
        <!--                         ></p> -->
        <!--                     </div> -->
        <!--                     <input -->
        <!--                         id="dropzone-file" -->
        <!--                         type="file" -->
        <!--                         class="hidden" -->
        <!--                     /> -->
        <!--                 </label> -->
        <!--             </div> -->
        <!--         </div> -->
        <!--         <div class="block flex"> -->
        <!--             <div class="ml-auto"> -->
        <!--                 <Button type="submit" class="mr-3" color="red" -->
        <!--                     >Lanjutkan ke RW</Button -->
        <!--                 > -->
        <!--                 <Button type="submit">Proses Pengaduan</Button> -->
        <!--             </div> -->
        <!--         </div> -->
        <!--     </form> -->
        <!-- </Modal> -->

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

{#if selected}
    <Edit
        bind:showState={modalDetailPengaduan}
        bind:target={selected}
        on:comp={rebuild}
    />
{/if}
