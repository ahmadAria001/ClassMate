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
    import { type UpdateSchema, updateSchema } from "@R/Utils/Schema/RT/Update";

    import { twMerge } from "tailwind-merge";
    import { createEventDispatcher, onMount } from "svelte";
    import { page } from "@inertiajs/svelte";

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });

    export let showState = false;
    export let target: string = "";

    let qualifiedCandidates: any[] | undefined = [];
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    let selected: any = "";

    const getRT = async (id: string = "") => {
        const response = await axios.get(`/api/rt/${encodeURIComponent(id)}`);

        return response.data;
    };

    const getCivils = async () => {
        const response = await axios.get("/api/user");

        return response.data;
    };

    const filterCandidates = async () => {
        const rt = await getRT();
        const civils = await getCivils();
        const targetRT = await getRT(target);

        if (civils.data.length < 1) return;

        let resultArray: any[] = [];
        let leaders: any[] = [];

        rt.data.map((value: any) => {
            value.leader_id ? leaders.push(value.leader_id.id) : null;
        });

        civils.data.filter((item: any) => {
            leaders.includes(item.id) ? null : resultArray.push(item);

            if (item.id == targetRT.data[0]?.leader_id?.id) {
                resultArray.push(item);

                selected = item.id;
            }
        });

        return resultArray;
    };

    const { form, isSubmitting, errors } = createForm<UpdateSchema>({
        extend: validator<UpdateSchema>({
            schema: updateSchema,
        }),
        onSubmit: async (values) => {
            const response = await axios.put("/api/rt", {
                id: values.id,
                number: values.number,
                leader_id: values.leader_id,
                _token: $page.props.csrf_token,
            });

            err = response.data;
            showState = false;
            dispatch("comp");

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

        data?.map((value: any) => {
            qualifiedCandidates?.push({
                value: value.id,
                name: `${value.civilian_id.fullName} | RT. ${value.civilian_id.rt_id.number}`,
            });
        });

        return qualifiedCandidates;
    };

    onMount(async () => {
        await buildOptions();
    });
</script>

<Modal title="Edit RT" bind:open={showState}>
    {#await getRT(target) then data}
        <!-- {#await buildOptions() then ops} -->
        <form method="POST" use:form>
            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-4">
                    <Label for="leader" class="mb-2">Ketua RT</Label>
                    <Select
                        class="my-2"
                        size="sm"
                        id="leader"
                        placeholder="Ketua RT"
                        name="leader_id"
                    >
                        {#if qualifiedCandidates}
                            {#each qualifiedCandidates as { value, name }, idx}
                                {#if value == selected}
                                    <option {value} selected>{name}</option>
                                {:else}
                                    <option {value}>{name}</option>
                                {/if}
                            {/each}
                        {/if}
                    </Select>

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
                        value={data.data[0].number}
                    />
                    {#if $errors.number}
                        <span class="text-sm text-red-500"
                            >{$errors.number}</span
                        >
                    {/if}
                </div>
                <div class="mb-4 hidden">
                    <Label for="number" class="mb-2">ID</Label>
                    <Input type="number" name="id" readonly value={target} />
                    {#if $errors.id}
                        <span class="text-sm text-red-500">{$errors.id}</span>
                    {/if}
                </div>
            </div>
            <div class="block flex">
                <Button type="submit" class="ml-auto" disabled={$isSubmitting}
                    >Simpan</Button
                >
            </div>
        </form>
        <!-- {/await} -->
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
