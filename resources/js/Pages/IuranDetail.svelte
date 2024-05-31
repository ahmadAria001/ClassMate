<script lang="ts">
    import Layout from "./Layout.svelte";
    import { onMount } from "svelte";
    import axiosInstance from "axios";
    import {
        Button,
        Card,
        Table,
        Input,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        Badge,
        TabItem,
        TableSearch,
        Tabs,
        Checkbox,
        Modal,
    } from "flowbite-svelte";
    // import { Select, Label } from "flowbite-svelte";
    import { page, router } from "@inertiajs/svelte";
    import { writable } from "svelte/store";

    const axios = axiosInstance.create();

    let clickOutsideModal = false;
    let checkedAll = false;
    let checkedItems = Array(12).fill(false);
    let isAnyChecked = writable(false);
    let civilian: string | null;
    let rt: string | null;
    let duesTypes: any[] | null;
    let currentPage = 1;

    function toggleAll(event: Event) {
        const target = event.target as HTMLInputElement;
        checkedItems = checkedItems.map(() => target.checked);
        checkedAll = target.checked;
        isAnyChecked.set(checkedAll);
    }

    function toggleItem(index: number) {
        return function (event: Event) {
            const target = event.target as HTMLInputElement;
            checkedItems[index] = target.checked;
            checkedAll = checkedItems.every(Boolean);
            isAnyChecked.set(checkedItems.some(Boolean));
        };
    }

    const getDuesTypes = async (filter: string) => {
        try {
            const response = await axios.get(
                `/api/dues/types/${encodeURIComponent(filter)}`,
                {
                    headers: {
                        Accept: "application/json",
                    },
                },
            );

            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const getDuesMember = async (member: string, dues: string) => {
        try {
            const response = await axios.get(
                `/api/dues/member/${encodeURIComponent(member)}/${encodeURIComponent(dues)}/${currentPage}`,
                {
                    headers: {
                        Accept: "application/json",
                    },
                },
            );

            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const initPage = async () => {
        if (!rt) return;

        const types = await getDuesTypes(rt);
        duesTypes = types.data;
    };

    onMount(async () => {
        extractParams();
        await initPage();
    });

    const extractParams = () => {
        const urlParams = new URLSearchParams(location.search);
        civilian = urlParams.get("civ");
        rt = urlParams.get("rt");

        if (!rt) router.visit("/404");
        if (!civilian) router.visit("/404");
    };

    const dateFormatter = (epoc: number) => {
        const date = new Date(epoc);

        const monthNames = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];

        const day = date.getDate();

        const monthIndex = date.getMonth();
        const monthName = monthNames[monthIndex];

        const year = date.getFullYear();

        return `${day} ${monthName} ${year}`;
    };

    $: isAnyChecked.set(checkedItems.some(Boolean));
</script>

<Layout>
    <div class="flex justify-between flex-col lg:flex-row">
        <div>
            <div
                class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700 divide-gray-200 dark:divide-gray-700 shadow-md flex flex-col w-full lg:max-w-md"
            >
                <p
                    class="p-5 text-lg font-semibold text-left text-gray-900 bg-white dark:text-white dark:bg-gray-800 z-10"
                >
                    Informasi Warga
                </p>
                <Table
                    striped={true}
                    divClass="rounded-lg relative overflow-hidden"
                >
                    <TableBody tableBodyClass="divide-y max-w-xs">
                        <TableBodyRow>
                            <TableBodyCell>Nama</TableBodyCell>
                            <TableBodyCell class="w-full truncate max-w-xs"
                                >Joko Anwar Maulana Tedjo Cahyo Perdana</TableBodyCell
                            >
                        </TableBodyRow>
                        <TableBodyRow>
                            <TableBodyCell>KK</TableBodyCell>
                            <TableBodyCell>12345</TableBodyCell>
                        </TableBodyRow>
                        <TableBodyRow>
                            <TableBodyCell>Alamat</TableBodyCell>
                            <TableBodyCell>JL. Pahlawan no 456</TableBodyCell>
                        </TableBodyRow>
                        <TableBodyRow>
                            <TableBodyCell>Status Kependudukan</TableBodyCell>
                            <TableBodyCell
                                ><Badge color="green">Tetap</Badge
                                ></TableBodyCell
                            >
                        </TableBodyRow>
                    </TableBody>
                </Table>
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700 divide-gray-200 dark:divide-gray-700 shadow-md flex flex-col w-full flex-grow mt-4 lg:mt-0 lg:ml-4"
        >
            <div class="flex justify-between p-5">
                <p
                    class="text-lg font-semibold text-left text-gray-900 bg-white dark:text-white dark:bg-gray-800 z-10"
                >
                    Detail Iuran Warga
                </p>

                <!-- Tombol bayar sesuai yang di centang -->
                <Button
                    on:click={() => (clickOutsideModal = true)}
                    disabled={$isAnyChecked === false}>Bayar</Button
                >
            </div>
            <Tabs class="px-4">
                {#if duesTypes}
                    {#each duesTypes as d}
                        <TabItem
                            open
                            title={d.typeDues == "Security"
                                ? "Keamanan"
                                : d.typeDues == "TrashManagement"
                                  ? "Sampah"
                                  : d.typeDues == "TrashManagement"
                                    ? "Event"
                                    : ""}
                        >
                            <Table hoverable={true}>
                                <TableHead>
                                    <TableHeadCell class="!p-4">
                                        <Checkbox
                                            bind:checked={checkedAll}
                                            on:change={toggleAll}
                                        />
                                    </TableHeadCell>
                                    <TableHeadCell>Nama Bulan</TableHeadCell>
                                    <TableHeadCell>Tagihan</TableHeadCell>
                                    <TableHeadCell>Status</TableHeadCell>
                                    <!-- <TableHeadCell class="text-center"
                                    >Bayar</TableHeadCell
                                > -->
                                </TableHead>
                                <TableBody tableBodyClass="divide-y">
                                    {#if civilian}
                                        {#await getDuesMember(civilian, d.id) then data}
                                            {#each data.data as item, idx}
                                                <TableBodyRow>
                                                    <TableBodyCell class="!p-4">
                                                        <Checkbox
                                                            bind:checked={checkedItems[
                                                                idx
                                                            ]}
                                                            on:change={toggleItem(
                                                                idx,
                                                            )}
                                                        />
                                                    </TableBodyCell>
                                                    <TableBodyCell
                                                        >{dateFormatter(
                                                            item.paid_for *
                                                                1000,
                                                        )}</TableBodyCell
                                                    >
                                                    <TableBodyCell
                                                        >Rp. {item.amount_paid}</TableBodyCell
                                                    >

                                                    <!-- {#if item.residentstatus == "PermanentResident"}
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
                                        {/if} -->

                                                    <TableBodyCell
                                                        class="text-center"
                                                    >
                                                        <Badge color="green"
                                                            >Lunas</Badge
                                                        >
                                                    </TableBodyCell>

                                                    <!-- <TableBodyCell class="text-center">
                                            <Button
                                                on:click={() =>
                                                    (clickOutsideModal = true)}
                                                >Bayar</Button
                                            >
                                        </TableBodyCell> -->
                                                </TableBodyRow>
                                            {/each}
                                        {/await}
                                    {/if}
                                </TableBody>
                            </Table>
                        </TabItem>
                    {/each}
                {/if}
            </Tabs>
        </div>
    </div>
</Layout>

<Modal
    title="Detail Pembayaran"
    bind:open={clickOutsideModal}
    autoclose
    outsideclose
>
    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
        Apakah anda yakin akan pembayaran ini?
    </p>
    <Table>
        <TableHead>
            <TableHeadCell class="!p-4">
                <Checkbox bind:checked={checkedAll} on:change={toggleAll} />
            </TableHeadCell>
            <TableHeadCell>Nama Bulan</TableHeadCell>
            <TableHeadCell>Tagihan</TableHeadCell>
            <TableHeadCell>Status</TableHeadCell>
            <!-- <TableHeadCell class="text-center"
                                    >Bayar</TableHeadCell
                                > -->
        </TableHead>
        <TableBody tableBodyClass="divide-y">
            {#each Array(3) as _, index}
                <TableBodyRow>
                    <TableBodyCell class="!p-4">
                        <Checkbox
                            bind:checked={checkedItems[index]}
                            on:change={toggleItem(index)}
                        />
                    </TableBodyCell>
                    <TableBodyCell>Juli</TableBodyCell>
                    <TableBodyCell>Rp. 100.000</TableBodyCell>

                    <!-- {#if item.residentstatus == "PermanentResident"}
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
                                        {/if} -->

                    <TableBodyCell class="text-center">
                        <Badge color="green">Lunas</Badge>
                    </TableBodyCell>

                    <!-- <TableBodyCell class="text-center">
                                            <Button
                                                on:click={() =>
                                                    (clickOutsideModal = true)}
                                                >Bayar</Button
                                            >
                                        </TableBodyCell> -->
                </TableBodyRow>
            {/each}
        </TableBody>
    </Table>
    <hr />
    <div class="footer text-end">
        <Button color="red">Batal</Button>
        <Button on:click={() => alert('Handle "success"')}>Yakin</Button>
    </div>
</Modal>
