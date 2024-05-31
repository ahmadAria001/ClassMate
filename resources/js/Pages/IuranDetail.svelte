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
    import Payment from "@C/DetailIuran/Modals/Payment.svelte";
    import { twMerge } from "tailwind-merge";

    const axios = axiosInstance.create();

    let clickOutsideModal = false;
    let checkedAll = false;
    let checkedItems: any[] = [];
    let isAnyChecked = writable(false);
    let civilian: string;
    let rt: string;
    let duesTypes: any;
    let currentPage = 1;
    let civilianMdl: any;
    let paymentLog: any;
    let selected: any[] = [];
    let amountPay: number;

    let unpaidData: any[] = [];

    $: canBegine = civilian && rt ? true : false;

    $: detailIsExist = paymentLog ? true : false;

    function toggleAll(event: Event) {
        const target = event.target as HTMLInputElement;
        checkedItems = checkedItems.map(() => target.checked);
        checkedAll = target.checked;

        selected = [];
        if (target.checked) {
            selected = unpaidData;
        }

        isAnyChecked.set(checkedAll);
    }

    function offAll() {
        if (!detailIsExist) return;

        setTimeout(() => {
            const inputs = document.querySelectorAll('input[type="checkbox"]');
            inputs.forEach((element: any) => {
                element.checked = false;
            });

            // checkedItems = [];

            isAnyChecked.set(false);
        }, 250);
    }

    function toggleItem(index: number, stat: boolean) {
        return function (event: Event) {
            const target = event.target as HTMLInputElement;
            checkedItems[index] = target.checked;

            if (selected.includes(paymentLog.data[index]))
                selected.splice(index, 1);
            else selected.push(paymentLog.data[index]);

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

    const getCivil = async (filter: string) => {
        try {
            const response = await axios.get(
                `/api/civilian/${encodeURIComponent(filter)}`,
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
        if (rt.length < 1) return;
        if (civilian.length < 1) return;

        const types = await getDuesTypes(rt);
        const civilianData = await getCivil(civilian);

        duesTypes = types.data;
        civilianMdl = civilianData.data;
    };

    onMount(async () => {
        await extractParams();
        if (canBegine) await initPage();
    });

    const extractParams = async () => {
        const urlParams = new URLSearchParams(location.search);
        civilian = urlParams.get("civ")!;
        rt = urlParams.get("rt")!;

        if (rt.length < 1) return;
        if (civilian.length < 1) return;
    };

    let builder = {};
    const rebuild = async () => {
        await initPage();
        builder = {};
    };

    const generateUnpaid = (lastPaidDate: number) => {
        const paidMont = new Date(lastPaidDate * 1000).getMonth();
        const paidYear = new Date(lastPaidDate * 1000).getFullYear();
        const currentMont = new Date(Date.now()).getMonth();
        const currentYear = new Date(Date.now()).getFullYear();

        const generatedUnpainPayment: {
            paid_for: number;
            amount_paid: number;
        }[] = [];

        let loopMonth = paidMont + 1;
        let loopYear = paidYear;

        // console.log(new Date(lastPaidDate * 1000).getMonth());

        while (loopYear <= currentYear) {
            while (
                loopMonth <= currentMont ||
                (loopYear < currentYear && loopMonth <= 11)
            ) {
                let generatedDate = new Date(lastPaidDate * 1000);
                generatedDate = new Date(generatedDate.setMonth(loopMonth));
                generatedDate = new Date(generatedDate.setFullYear(loopYear));

                generatedUnpainPayment.push({
                    paid_for: generatedDate.getTime() / 1000,
                    amount_paid: 0,
                });

                if (loopMonth <= 11 && loopYear <= currentYear) {
                    loopMonth++;
                } else {
                    break;
                }
            }

            if (loopYear <= currentYear) {
                loopMonth = 0;

                loopYear++;
            } else {
                break;
            }
        }

        return generatedUnpainPayment.reverse();
    };

    const generatesPaymentLog = async (
        target: string,
        dues: string,
        stat: boolean,
    ) => {
        const duesDatas = await getDuesMember(target, dues);
        paymentLog = duesDatas;

        if (!stat) return;

        // console.log(new Date(duesDatas.data[0].paid_for * 1000).getMonth());

        if (duesDatas.data.length > 0) {
            const dummy = generateUnpaid(duesDatas.data[0].paid_for);
            unpaidData = dummy;

            paymentLog.data = dummy.concat(duesDatas.data);
            paymentLog.length = dummy.length;
        }

        // console.log(paymentLog);
    };

    const dateFormatter = (epoc: number) => {
        const date = new Date(epoc);

        const monthNames = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "Mei",
            "Jun",
            "Jul",
            "Agu",
            "Sep",
            "Okt",
            "Nov",
            "Des",
        ];

        const day = date.getDate();

        const monthIndex = date.getMonth();
        const monthName = monthNames[monthIndex];

        const year = date.getFullYear();

        return `${monthName} ${year}`;
    };

    const handleSelected = () => {};

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
                {#if civilianMdl}
                    <Table
                        striped={true}
                        divClass="rounded-lg relative overflow-hidden"
                    >
                        <TableBody tableBodyClass="divide-y max-w-xs">
                            <TableBodyRow>
                                <TableBodyCell>Nama</TableBodyCell>
                                <TableBodyCell class="w-full truncate max-w-xs"
                                    >{civilianMdl.fullName}</TableBodyCell
                                >
                            </TableBodyRow>
                            <TableBodyRow>
                                <TableBodyCell>KK</TableBodyCell>
                                <TableBodyCell>{civilianMdl.nkk}</TableBodyCell>
                            </TableBodyRow>
                            <TableBodyRow>
                                <TableBodyCell>Alamat</TableBodyCell>
                                <TableBodyCell
                                    >{civilianMdl.address}</TableBodyCell
                                >
                            </TableBodyRow>
                            <TableBodyRow>
                                <TableBodyCell
                                    >Status Kependudukan</TableBodyCell
                                >
                                <TableBodyCell>
                                    {#if civilianMdl.residentstatus == "PermanentResident"}
                                        <Badge color="green">Tetap</Badge>
                                    {:else if civilianMdl.residentstatus == "ContractResident"}
                                        <Badge color="indigo">Kontrak</Badge>
                                    {:else if civilianMdl.residentstatus == "Kos"}
                                        <Badge color="yellow">Kos</Badge>
                                    {/if}
                                </TableBodyCell>
                            </TableBodyRow>
                        </TableBody>
                    </Table>
                {/if}
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
                <!-- {#if duesTypes}
                    {#if duesTypes.status}
                    {/if}
                    {/if} -->
                <Button
                    on:click={() => {
                        clickOutsideModal = true;
                    }}
                    disabled={$isAnyChecked === false}>Bayar</Button
                >
            </div>
            <Tabs class="px-4">
                {#key builder}
                    {#if civilian != ""}
                        {#if duesTypes}
                            {#each duesTypes as d}
                                <TabItem
                                    on:focus={async (e) => {
                                        amountPay = d.amt_dues;

                                        await generatesPaymentLog(
                                            civilian,
                                            d.id,
                                            d.status,
                                        );

                                        // if ($isAnyChecked)
                                        offAll();
                                    }}
                                    title={d.typeDues == "Security"
                                        ? "Keamanan"
                                        : d.typeDues == "TrashManagement"
                                          ? "Sampah"
                                          : d.typeDues == "Event"
                                            ? "Acara"
                                            : ""}
                                    inactiveClasses={twMerge(
                                        "inline-block text-sm font-medium text-center disabled:cursor-not-allowed' p-4 text-gray-500 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-300",
                                        d.status
                                            ? ""
                                            : "text-red-500 dark:text-red-500",
                                    )}
                                >
                                    <Table hoverable={true}>
                                        <TableHead>
                                            <TableHeadCell class="!p-4">
                                                {#if d.status}
                                                    <Checkbox
                                                        bind:checked={checkedAll}
                                                        on:change={toggleAll}
                                                        disabled={!d.status}
                                                    />
                                                {/if}
                                            </TableHeadCell>
                                            <TableHeadCell
                                                >Nama Bulan</TableHeadCell
                                            >
                                            <TableHeadCell
                                                >Tagihan</TableHeadCell
                                            >
                                            <TableHeadCell>Status</TableHeadCell
                                            >
                                            <!-- <TableHeadCell class="text-center"
                                    >Bayar</TableHeadCell
                                > -->
                                        </TableHead>
                                        <TableBody tableBodyClass="divide-y">
                                            {#if paymentLog}
                                                {#each paymentLog.data as item, idx}
                                                    <TableBodyRow>
                                                        <TableBodyCell
                                                            class="!p-4"
                                                        >
                                                            {#if d.status && Number.parseInt(item.amount_paid) < Number.parseInt(d.amt_dues)}
                                                                <Checkbox
                                                                    bind:checked={checkedItems[
                                                                        idx
                                                                    ]}
                                                                    on:change={toggleItem(
                                                                        idx,
                                                                        item.amount_paid >=
                                                                            d.amt_dues,
                                                                    )}
                                                                />
                                                            {/if}
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
                                                            <!-- {console.log(
                                                                Number.parseInt(
                                                                    item.amount_paid,
                                                                ) <
                                                                    Number.parseInt(
                                                                        d.amt_dues,
                                                                    ),
                                                                Number.parseInt(
                                                                    item.amount_paid,
                                                                ),
                                                                Number.parseInt(
                                                                    d.amt_dues,
                                                                ),
                                                            )} -->
                                                            {#if Number.parseFloat(item.amount_paid) >= Number.parseFloat(d.amt_dues)}
                                                                <Badge
                                                                    color="green"
                                                                >
                                                                    Lunas
                                                                </Badge>
                                                            {:else}
                                                                <Badge
                                                                    color="red"
                                                                >
                                                                    Belum Lunas
                                                                </Badge>
                                                            {/if}
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
                                            {/if}
                                        </TableBody>
                                    </Table>
                                </TabItem>
                            {/each}
                        {/if}
                    {/if}
                {/key}
            </Tabs>
        </div>
    </div>
</Layout>

{#if selected && clickOutsideModal}
    <Payment
        bind:showState={clickOutsideModal}
        on:comp={rebuild}
        bind:selected
        bind:amountPay
    />
{/if}
