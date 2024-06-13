<script lang="ts">
    import { onMount } from "svelte";
    import Layout from "./Layout.svelte";
    import {
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
        Textarea,
        Table,
    } from "flowbite-svelte";
    import TableSearch from "@C/General/TableSearch.svelte";
    import { page } from "@inertiajs/svelte";
    import {
        ChartLineDownOutline,
        ChartLineUpOutline,
        ChevronLeftOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";
    import CardInfo from "@C/HomePage/CardInfo.svelte";
    import Detail from "@C/Spending/Modals/Detail.svelte";
    import Create from "@C/Spending/Modals/Create.svelte";
    import Delete from "@C/Spending/Modals/Delete.svelte";
    import Edit from "@C/Spending/Modals/Edit.svelte";

    import axiosInstance from "axios";

    const axios = axiosInstance.create({ withCredentials: true });
    let items = [
        {
            id: 1,
            name: "Kerja Bakti",
            location: "Jl. Rambutan - Jl. Mangga",
            src: "https://cdn1.epicgames.com/ue/product/Screenshot/Screenshot23-1920x1080-a2f8a3c1f88f3b5716bdd8c9a2ea0c28.jpg?resize=1&w=1920",
            title: "Nyoba",
            desc: "Menyala Abangku",
        },
    ];
    let addAnnoucement = false;
    let modalEdit = false;
    let modalPreview = false;
    let deleteModal = false;
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
    let selected = "";
    let data: any;
    let duesIncome: any;
    let totalDues: any;
    let paymentDues: any;
    let totalOutcome: any;
    let id_rt = $page.props.auth.user.rt_id;

    let builder = {};
    export const rebuild = async () => {
        console.log("rebuild called");

        await initData();
        builder = {};
        filteredData = data.data;
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

    const role = $page.props.auth.user.role;

    const getSpendigLog = async () => {
        let url = "";

        if (role == "RT")
            url = `/api/spending/rt/${encodeURIComponent(currentPage)}`;
        if (role != "RT" && role != "Warga")
            url = `/api/spending/p/${encodeURIComponent(currentPage)}`;

        if (url == "") return;

        const response = await axios.get(url, {
            headers: {
                Accept: "application/json",
            },
        });

        return response.data;
    };

    const getTotalSpending = async () => {
        try {
            const response = await getSpendigLog();
            const spendingData = response.data;

            if (Array.isArray(spendingData)) {
                const totalSpending = spendingData.reduce((total, spending) => {
                    return total + parseFloat(spending.amount);
                }, 0);

                return totalSpending;
            } else {
                console.error("Data is not an array or is undefined");
                return 0;
            }
        } catch (error) {
            console.error("Error fetching spending data:", error);
            return 0;
        }
    };

    const getTotalDataDues = async () => {
        try {
            const response = await axios.get(`/api/dues-payment/rt/${id_rt}`, {
                headers: {
                    Accept: "application/json",
                },
            });

            // console.log("Dues payment response:", response);
            const paymentDues = response.data.data;
            // console.log("paymentDues:", paymentDues);

            if (Array.isArray(paymentDues)) {
                const totalDues = paymentDues.reduce(
                    (total: number, dues: any) => {
                        const amountPaid = parseFloat(dues.amount_paid);
                        // console.log("amountPaid:", amountPaid);
                        if (!isNaN(amountPaid)) {
                            return total + amountPaid;
                        } else {
                            console.error(
                                "Invalid amount_paid:",
                                dues.amount_paid,
                            );
                            return total;
                        }
                    },
                    0,
                );

                return totalDues;
            } else {
                console.error("paymentDues is not an array or is undefined");
                return 0;
            }
        } catch (error) {
            console.error("Error fetching dues data:", error);
            return 0;
        }
    };

    const initData = async () => {
        data = await getSpendigLog();
        duesIncome = await getTotalDataDues();
        totalOutcome = await getTotalSpending();
        console.log(data);
    };

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item.name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );

    onMount(async () => {
        try {
            // Call renderPagination when the component initially mounts
            renderPagination(items.length);
            await initData();
            filteredData = data.data;
            // console.log(filteredData);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });

    let filteredData: any;
    const handleSearch = (event: any) => {
        const searchValue = event.detail.value.toLowerCase();
        // console.log("Search value in handleSearch in use file:", searchValue);
        if (searchValue == "") {
            filteredData = [data.data];
        }
        filteredData = data.data.filter((d: any) =>
            d.title.toLowerCase().includes(searchValue),
        );
        console.log(filteredData);
        rebuild();
    };
</script>

<Layout>
    <div
        class="info-card grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8"
    >
        <CardInfo
            title="Pemasukan"
            value={duesIncome}
            icon={ChartLineUpOutline}
            iconBgClass="bg-green-700 dark:bg-green-500"
        />
        <CardInfo
            title="Pengeluaran"
            value={totalOutcome}
            icon={ChartLineDownOutline}
            iconBgClass="bg-red-700 dark:bg-red-500"
        />
    </div>
    <TableSearch on:search={handleSearch}>
        <div
            slot="header"
            class="md:w-auto flex flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
        >
            <Button
                on:click={() => {
                    addAnnoucement = true;
                }}>+ Tambah Pengeluaran</Button
            >
        </div>
        <Table>
            <TableHead>
                <TableHeadCell>Pembuat</TableHeadCell>
                <TableHeadCell>Jumlah</TableHeadCell>
                <TableHeadCell>Tanggal Buat</TableHeadCell>
                <TableHeadCell class="sr-only">Aksi</TableHeadCell>
            </TableHead>
            <TableBody>
                {#key builder}
                    {#if filteredData}
                        {#each filteredData as item}
                            <TableBodyRow>
                                <TableBodyCell
                                    >{item.created_by.civilian_id
                                        .fullName}</TableBodyCell
                                >
                                <TableBodyCell>Rp. {item.amount}</TableBodyCell>
                                <TableBodyCell
                                    >{new Date(
                                        item.created_at,
                                    ).toLocaleDateString()}</TableBodyCell
                                >
                                <TableBodyCell class="text-end">
                                    <Button
                                        color="blue"
                                        on:click={() => {
                                            selected = item.id;
                                            modalPreview = true;
                                        }}>Preview</Button
                                    >
                                    <Button
                                        color="blue"
                                        class="ms-2"
                                        on:click={() => {
                                            modalEdit = true;
                                            selected = item.id;
                                        }}>Edit Data</Button
                                    >
                                    <Button
                                        type="button"
                                        on:click={() => {
                                            selected = item.id;
                                            deleteModal = true;
                                        }}
                                        class="ms-2"
                                        color="red">Hapus</Button
                                    >
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
            {#if filteredData}
                <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                >
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {currentPage < 2
                            ? 1
                            : filteredData.length < 5
                              ? data.length - filteredData.length + 1
                              : filteredData.length + 1}
                        -
                        {filteredData.length < 5
                            ? data.length
                            : filteredData.length * currentPage}
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
                            await initData();
                        }}><ChevronLeftOutline /></Button
                    >
                    <!-- {#each data.length as pageNumber} -->
                    <Button disabled>{currentPage}</Button>
                    <!-- {/each} -->
                    <Button
                        disabled={currentPage >= data.length / 5}
                        on:click={async () => {
                            currentPage++;
                            await initData();
                        }}><ChevronRightOutline /></Button
                    >
                </ButtonGroup>
            {/if}
        </div>
    </TableSearch>
</Layout>

<Create bind:showState={addAnnoucement} on:comp={rebuild} />

{#if selected && modalPreview}
    <Detail
        bind:showState={modalPreview}
        bind:items={selected}
        on:comp={rebuild}
    />
{/if}

{#if selected && modalEdit}
    <Edit bind:showState={modalEdit} bind:target={selected} on:comp={rebuild} />
{/if}

{#if selected && deleteModal}
    <Delete
        bind:showState={deleteModal}
        bind:target={selected}
        on:comp={rebuild}
    />
{/if}
