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
    } from "flowbite-svelte";
    import { onMount } from "svelte";
    import { twMerge } from "tailwind-merge";

    export let showState = false;
    export let selected: any[];
    export let amountPay: number;

    let total = 0;

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

    onMount(() => {
        if (selected.length > 0) {
            total = selected.length * amountPay;
        }
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
        <Button color="red">Batal</Button>
        <Button on:click={() => alert('Handle "success"')}>Yakin</Button>
    </div>
</Modal>
