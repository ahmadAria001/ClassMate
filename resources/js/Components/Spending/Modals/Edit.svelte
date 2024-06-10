<script lang="ts">
    import {
        Button,
        Input,
        Label,
        Modal,
        Select,
        Textarea,
        Toast,
    } from "flowbite-svelte";

    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";
    import { createEventDispatcher, onMount } from "svelte";
    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";

    import {
        type UpdateSchema,
        updateSchema,
    } from "@R/Utils/Schema/Spendings/Update";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    export let showState = false;
    export let target: string;

    const axios = axiosInstance.create({ withCredentials: true });
    const dispatch = createEventDispatcher();

    let err: { status: null | boolean; message: null | string };

    let data: any;

    const { form, isSubmitting, errors } = createForm<UpdateSchema>({
        extend: validator<UpdateSchema>({
            schema: updateSchema,
        }),
        onSubmit: async (values) => {
            const body = {
                _method: "PUT",
                id: target,
                category: values.category,
                desc: values.desc,
                amount: values.amount,
                _token: $page.props.csrf_token,
            };

            const response = await axios.post("/api/spending", body);
            console.log(response.data);

            err = response.data;
            showState = false;
            dispatch("comp");

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        },
        onError: (values: any) => {
            err = {
                message: values?.response?.data?.message,
                status: values?.response?.data?.status,
            };
            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            console.error(values);

            return;
        },
        onSuccess: () => {
            console.log("Success");
        },
    });

    const getTarget = async (id: string = "") => {
        const response = await axios.get(
            `/api/spending/${encodeURIComponent(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const initialLoad = async () => {
        data = await getTarget(target);
    };

    onMount(async () => {
        await initialLoad();
    });

    const categoty = [
        { name: "Acara", value: "Event" },
        { name: "Administrasi", value: "Administration" },
        { name: "Infrastruktur/Pembangunan", value: "Infrastructure" },
    ];
</script>

<Modal
    title="Edit Pengambilan Dana"
    bind:open={showState}
    on:close={() => {
        showState = false;
    }}
>
    {#if data}
        <form method="POST" use:form>
            <div class="mb-4">
                <Label for="category" class="mb-2">Kategori</Label>
                <Select
                    id="category"
                    name="category"
                    placeholder="Kategori"
                    items={categoty}
                    value={data.data.category}
                />
                {#if $errors.category}
                    <span class="text-sm text-red-500">{$errors.category}</span>
                {/if}
            </div>
            <div class="mb-4">
                <Label for="desc" class="mb-2">Keterangan</Label>
                <Textarea
                    rows="2"
                    id="desc"
                    name="desc"
                    placeholder="Isi Pengumuman"
                    value={data.data.desc}
                />
                {#if $errors.desc}
                    <span class="text-sm text-red-500">{$errors.desc}</span>
                {/if}
            </div>
            <div class="mb-4">
                <Label for="amount" class="mb-2">Jumlah</Label>
                <Input
                    type="number"
                    id="amount"
                    name="amount"
                    placeholder="Isi Pengumuman"
                    value={data.data.amount}
                />
                {#if $errors.amount}
                    <span class="text-sm text-red-500">{$errors.amount}</span>
                {/if}
            </div>
            <div class="flex justify-end align-middle">
                <Button type="submit" class="ml-auto" disabled={!isSubmitting}
                    >Simpan</Button
                >
            </div>
        </form>
    {/if}
</Modal>

{#if err && err.status == true}
    <Toast color="green" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err && err.status == false}
    <Toast color="red" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
