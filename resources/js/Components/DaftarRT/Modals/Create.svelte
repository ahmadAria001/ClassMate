<script lang="ts">
    import axiosInstance from "axios";

    import {
        Modal,
        Input,
        Label,
        Button,
        Select,
        Toggle,
        Toast,
    } from "flowbite-svelte";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    import { createForm } from "felte";
    import { validateSchema, validator } from "@felte/validator-zod";

    import { twMerge } from "tailwind-merge";
    import { createEventDispatcher, onMount } from "svelte";
    import { page } from "@inertiajs/svelte";

    import { type CreateSchema, createSchema } from "@U/Schema/RT/Create";

    const axios = axiosInstance.create({ withCredentials: true });
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const dispatch = createEventDispatcher();
    export let showState = false;

    let qualifiedCandidates: any[] | undefined = [];

    const getRT = async () => {
        const response = await axios.get(`/api/rt`);

        return response.data;
    };

    const getCivils = async () => {
        const response = await axios.get("/api/user");

        return response.data;
    };

    const filterCandidates = async () => {
        const rt = await getRT();
        const civils = await getCivils();

        if (civils.data.length < 1) return;

        let resultArray: any[] = [];
        let leaders: any[] = [];

        rt.data.map((value: any) => {
            value.leader_id ? leaders.push(value.leader_id.id) : null;
        });

        civils.data.filter((item: any) => {
            leaders.includes(item.id) ? null : resultArray.push(item);
        });

        return resultArray;
    };

    const { form, isSubmitting, errors } = createForm<CreateSchema>({
        extend: validator<CreateSchema>({
            schema: createSchema,
        }),
        onSubmit: async (values) => {
            const response = await axios.post("/api/rt", {
                number: values.number,
                leader_id: values.leader_id,
                _token: $page.props.csrf_token,
            });

            err = response.data;
            showState = false;
            dispatch("comp");
            console.log(response.data);

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        },
        onError: (values: unknown) => {
            console.error(values?.response);
            err = values?.response?.data;

            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            return;
        },
        onSuccess: (response) => {
            console.log("Success");
        },
    });

    const buildOptions = async () => {
        const data = await filterCandidates();

        qualifiedCandidates = [];

        data?.map((value: any) =>
            qualifiedCandidates?.push({
                value: value.id,
                name: `${value.civilian_id.fullName} | RT. ${value.civilian_id.rt_id.number}`,
            }),
        );
    };
</script>

<Modal title="Tambah RT" bind:open={showState}>
    {#await buildOptions() then data}
        <form method="POST" use:form>
            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-4">
                    <Label for="leader" class="mb-2">Ketua RT</Label>
                    <Select
                        class="my-2"
                        items={qualifiedCandidates}
                        size="sm"
                        id="leader"
                        placeholder="Ketua RT"
                        name="leader_id"
                    />

                    {#if $errors.leader_id}
                        <span class="text-sm text-red-500"
                            >{$errors.leader_id}</span
                        >
                    {/if}
                </div>
                <div class="mb-4">
                    <Label for="number" class="mb-2">Nomor RT</Label>
                    <Input
                        id="number"
                        placeholder="Alamat"
                        name="number"
                        type="number"
                    />
                    {#if $errors.number}
                        <span class="text-sm text-red-500"
                            >{$errors.number}</span
                        >
                    {/if}
                </div>
            </div>
            <div class="block flex">
                <Button type="submit" class="ml-auto" disabled={$isSubmitting}
                    >Simpan</Button
                >
            </div>
        </form>
    {/await}
</Modal>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
