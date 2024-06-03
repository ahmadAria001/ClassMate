<script lang="ts">
    import {
        Badge,
        Button,
        Modal,
        Table,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        Toast,
    } from "flowbite-svelte";
    import { twMerge } from "tailwind-merge";

    import axiosInstance from "axios";
    import { page } from "@inertiajs/svelte";
    import { createEventDispatcher, onMount } from "svelte";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    export let showState = false;
    export let selected: {
        paid_for: number;
        amount_paid: number;
        dues_member: number;
    }[];
    export let amountPay: number;

    let total = 0;

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });

    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
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

        // const day = date.getDate();

        const monthIndex = date.getMonth();
        const monthName = monthNames[monthIndex];

        const year = date.getFullYear();

        return `${monthName} ${year}`;
    };

    const submitPayment = async () => {
        try {
            let body = {
                payments: selected,
            };

            console.log(body);

            const response = await axios.post(`/api/dues-payment/`, body, {
                headers: {
                    Accept: "application/json",
                },
            });

            console.log(response.data);

            err = response.data;
            showState = false;
            dispatch("comp");

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        } catch (error) {
            console.error(error);
        }
    };

    onMount(() => {
        selected.map(
            (value) =>
                (value.amount_paid = Number.parseFloat(amountPay.toString())),
        );

        if (selected.length > 0) {
            total = selected.length * amountPay;
        }

        console.log(amountPay);
    });
</script>

<Modal
    title="Detail Pembayaran"
    bind:open={showState}
    on:close={() => (showState = false)}
    autoclose
    outsideclose
>
    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
        Apakah anda yakin akan pembayaran ini?
    </p>
    <Table>
        <TableHead>
            <TableHeadCell>Nama Bulan</TableHeadCell>
            <TableHeadCell>Tagihan</TableHeadCell>
            <TableHeadCell>Status</TableHeadCell>
            <!-- <TableHeadCell class="text-center"
                                    >Bayar</TableHeadCell
                                > -->
        </TableHead>
        <TableBody tableBodyClass="divide-y">
            {#if selected.length > 0}
                {#each selected as item, index}
                    <TableBodyRow>
                        <TableBodyCell
                            >{dateFormatter(
                                item.paid_for * 1000,
                            )}</TableBodyCell
                        >
                        <TableBodyCell>Rp. {amountPay}</TableBodyCell>

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
                <TableBodyRow color="custom" class="bg-gray-900">
                    <TableBodyCell>
                        <span>Total</span>
                    </TableBodyCell>
                    <TableBodyCell
                        tdClass={twMerge(
                            "px-6 py-4 whitespace-nowrap font-medium ",
                            "col-span-4 pt-3 grid grid-cols-1",
                        )}
                    >
                        <Badge color="green">Rp. {total}</Badge>
                    </TableBodyCell>
                    <TableBodyCell class=""></TableBodyCell>
                </TableBodyRow>
            {/if}
        </TableBody>
    </Table>
    <hr />
    <div class="footer text-end">
        <Button color="red" on:click={() => (showState = false)}>Batal</Button>
        <Button
            on:click={async () => {
                await submitPayment();
            }}>Yakin</Button
        >
    </div>
</Modal>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-10 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-10 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
